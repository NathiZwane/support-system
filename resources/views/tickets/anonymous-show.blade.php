<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Details - {{ $ticket->ticket_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Ticket Details</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Ticket Number:</strong>
                                <p class="text-muted">{{ $ticket->ticket_number }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <p>
                                    <span class="badge 
                                        @if($ticket->status == 'newly_logged') bg-primary
                                        @elseif($ticket->status == 'in_progress') bg-warning
                                        @elseif($ticket->status == 'resolved') bg-success
                                        @else bg-secondary @endif">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name:</strong>
                                <p class="text-muted">{{ $ticket->first_name }} {{ $ticket->last_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p class="text-muted">{{ $ticket->email }}</p>
                            </div>
                        </div>

                        @if($ticket->phone)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Phone:</strong>
                                <p class="text-muted">{{ $ticket->phone }}</p>
                            </div>
                        </div>
                        @endif

                        @if($ticket->company)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Company:</strong>
                                <p class="text-muted">{{ $ticket->company }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Category:</strong>
                                <p class="text-muted text-uppercase">{{ $ticket->category }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Subject:</strong>
                                <p class="text-muted">{{ $ticket->subject }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Description:</strong>
                                <div class="border p-3 bg-light">
                                    {{ $ticket->description }}
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Created:</strong>
                                <p class="text-muted">{{ $ticket->created_at->format('M j, Y g:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Last Updated:</strong>
                                <p class="text-muted">{{ $ticket->updated_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        @if($ticket->latitude && $ticket->longitude)
                        <div class="row mb-3">
                            <div class="col-12">
                                <strong>Location:</strong>
                                <p class="text-muted">
                                    Latitude: {{ $ticket->latitude }}, Longitude: {{ $ticket->longitude }}
                                </p>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create New Ticket</a>
                            <a href="{{ url('/') }}" class="btn btn-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>

                <!-- Ticket Activities -->
  @if($ticket->activities->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
             <h5 class="mb-0">Ticket History</h5>
                 </div>
             <div class="card-body">
              <div class="timeline">
            @foreach($ticket->activities->sortByDesc('id') as $activity)
            <div class="timeline-item mb-3">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <span class="badge bg-info">
                            {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <p class="mb-1">{{ $activity->description }}</p>
                        <small class="text-muted">
                            @if($activity->user)
                                By: {{ $activity->user->name }} - 
                            @endif
                            {{ $activity->created_at ? $activity->created_at->format('M j, Y g:i A') : 'N/A' }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>