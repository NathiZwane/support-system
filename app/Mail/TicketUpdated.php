<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Support Ticket Updated - ' . $this->ticket->ticket_number)
                    ->view('emails.ticket-updated')
                    ->with([
                        'ticket' => $this->ticket,
                        'anonymousUrl' => route('tickets.anonymous.show', $this->ticket->ticket_number)
                    ]);
    }
}