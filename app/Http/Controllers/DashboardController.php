<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $stats = [];

        if ($user->isAdmin() || $user->isSupportAgent()) {
            $stats = [
                'total_tickets' => SupportTicket::count(),
                'new_tickets' => SupportTicket::where('status', 'newly_logged')->count(),
                'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
                'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
                'tickets_by_category' => SupportTicket::select('category', DB::raw('COUNT(*) as count'))
                    ->groupBy('category')
                    ->get()
            ];

            // Recent tickets for support agents/admins
            $recentTickets = SupportTicket::with('loggedBy')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        } else {
            // Customer dashboard - show their own tickets
            $recentTickets = SupportTicket::where('email', $user->email)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        return view('dashboard', compact('stats', 'recentTickets', 'user'));
    }
}