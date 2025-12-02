<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Ticket Updated</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .footer { background: #6c757d; color: white; padding: 10px; text-align: center; }
        .ticket-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .status-new { color: #ffc107; font-weight: bold; }
        .status-progress { color: #17a2b8; font-weight: bold; }
        .status-resolved { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Support Ticket Updated</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $ticket->first_name }},</p>
            
            <p>Your support ticket status has been updated. Here are the current details:</p>
            
            <div class="ticket-info">
                <h3>Ticket #: {{ $ticket->ticket_number }}</h3>
                <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
                <p><strong>Category:</strong> {{ ucfirst($ticket->category) }}</p>
                <p><strong>Status:</strong> 
                    @if($ticket->status === 'newly_logged')
                        <span class="status-new">Newly Logged</span>
                    @elseif($ticket->status === 'in_progress')
                        <span class="status-progress">In Progress</span>
                    @else
                        <span class="status-resolved">Resolved</span>
                    @endif
                </p>
                <p><strong>Last Updated:</strong> {{ $ticket->updated_at->format('F j, Y g:i A') }}</p>
            </div>
            
            <p>You can check the current status of your ticket using the following link:</p>
            
            <p style="text-align: center;">
                <a href="{{ $anonymousUrl }}" class="btn">View Ticket Status</a>
            </p>
            
            <p>Or copy and paste this URL in your browser:<br>
            <small>{{ $anonymousUrl }}</small></p>
            
            <p>Thank you for your patience.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Support System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>