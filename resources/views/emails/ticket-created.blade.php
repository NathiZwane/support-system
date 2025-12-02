<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support Ticket Created</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #007bff; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; }
        .footer { background: #6c757d; color: white; padding: 10px; text-align: center; }
        .ticket-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #007bff; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Support Ticket Created</h1>
        </div>
        
        <div class="content">
            <p>Hello {{ $ticket->first_name }},</p>
            
            <p>Your support ticket has been successfully created. Here are your ticket details:</p>
            
            <div class="ticket-info">
                <h3>Ticket #: {{ $ticket->ticket_number }}</h3>
                <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
                <p><strong>Category:</strong> {{ ucfirst($ticket->category) }}</p>
                <p><strong>Status:</strong> <span style="color: #ffc107; font-weight: bold;">Newly Logged</span></p>
                <p><strong>Created:</strong> {{ $ticket->created_at->format('F j, Y g:i A') }}</p>
            </div>
            
            <p>You can check the status of your ticket at any time using the following link:</p>
            
            <p style="text-align: center;">
                <a href="{{ $anonymousUrl }}" class="btn">View Ticket Status</a>
            </p>
            
            <p>Or copy and paste this URL in your browser:<br>
            <small>{{ $anonymousUrl }}</small></p>
            
            <p>We'll notify you when there are updates to your ticket.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Support System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>