<?php

namespace App\Mail;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Support Ticket Created - ' . $this->ticket->ticket_number)
                    ->view('emails.ticket-created')
                    ->with([
                        'ticket' => $this->ticket,
                        'anonymousUrl' => route('tickets.anonymous.show', $this->ticket->ticket_number)
                    ]);
    }
}