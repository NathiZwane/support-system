<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\TicketCreated;
use App\Mail\TicketUpdated;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['create', 'store', 'showAnonymous']);
    }

    public function index(Request $request)
    {
        $query = SupportTicket::query();

        // Filter by date range
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordering
        $orderBy = $request->get('order_by', 'created_at');
        $orderDirection = $request->get('order_direction', 'desc');

        $validOrderColumns = ['first_name', 'last_name', 'created_at', 'status'];
        if (in_array($orderBy, $validOrderColumns)) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        
        // Security: Input validation with proper sanitization
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email:rfc,dns',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'category' => 'required|in:sales,accounts,it',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'gps_coordinates' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Parse GPS coordinates
        $latitude = null;
        $longitude = null;
        if ($request->filled('gps_coordinates')) {
            $coordinates = explode(',', $request->gps_coordinates);
            if (count($coordinates) === 2) {
                $latitude = floatval(trim($coordinates[0]));
                $longitude = floatval(trim($coordinates[1]));
            }
        }

        // Create ticket
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'category' => $request->category,
            'first_name' => strip_tags($request->first_name), // XSS protection
            'last_name' => strip_tags($request->last_name), // XSS protection
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => strip_tags($request->company), // XSS protection
            'subject' => strip_tags($request->subject), // XSS protection
            'description' => $request->description, // XSS handled in model mutator
            'latitude' => $latitude,
            'longitude' => $longitude,
            'logged_by' => Auth::check() ? Auth::id() : null,
            
        ]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'activity_type' => 'ticket_created',
            'description' => 'Ticket created successfully'
        ]);

        // Send email notification
        try {
            Mail::to($ticket->email)->send(new TicketCreated($ticket));
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket creation email: ' . $e->getMessage());
        }

        return redirect()->route('tickets.anonymous.show', ['ticketNumber' => $ticket->ticket_number])
            ->with('success', 'Ticket created successfully. Check your email for tracking details.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        if (!Auth::user()->isSupportAgent() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:newly_logged,in_progress,resolved'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $oldStatus = $ticket->status;
        $ticket->update(['status' => $request->status]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'activity_type' => 'status_updated',
            'description' => "Status changed from {$oldStatus} to {$request->status}"
        ]);

        // Send status update email
        try {
            Mail::to($ticket->email)->send(new TicketUpdated($ticket));
        } catch (\Exception $e) {
            \Log::error('Failed to send status update email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Ticket status updated successfully.');
    }

    public function showAnonymous($ticketNumber)
    {
      
          $ticket = SupportTicket::with('activities.user') // Eager load activities with user
                          ->where('ticket_number', $ticketNumber)
                          ->firstOrFail();
        
        return view('tickets.anonymous-show', compact('ticket'));
    }

    public function show(SupportTicket $ticket)
    {
        // Security: Only allow support agents, admins, or the ticket owner to view
        if (Auth::check()){
            if (!Auth::user()->isSupportAgent() && !Auth::user()->isAdmin()){
                abort(403, 'Unauthorized action.');
            }
        }
        
        $ticket->load(['activities.user', 'loggedBy']);
        return view('tickets.show', compact('ticket'));
    }
}