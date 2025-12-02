@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Ticket Details -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Ticket Details - {{ $ticket->ticket_number }}</h4>
                @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
                    <button type="button" 
                            class="btn btn-outline-success btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#statusModal">
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Customer Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $ticket->first_name }} {{ $ticket->last_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $ticket->email }}</td>
                            </tr>
                            @if($ticket->phone)
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $ticket->phone }}</td>
                            </tr>
                            @endif
                            @if($ticket->company)
                            <tr>
                                <td><strong>Company:</strong></td>
                                <td>{{ $ticket->company }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Ticket Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize">{{ $ticket->category }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Priority:</strong></td>
                                <td>
                                    @if($ticket->priority === 'high')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($ticket->priority === 'medium')
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($ticket->status === 'newly_logged')
                                        <span class="badge bg-warning">Newly Logged</span>
                                    @elseif($ticket->status === 'in_progress')
                                        <span class="badge bg-info">In Progress</span>
                                    @else
                                        <span class="badge bg-success">Resolved</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created:</strong></td>
                                <td>{{ $ticket->created_at->format('F j, Y g:i A') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $ticket->updated_at->format('F j, Y g:i A') }}</td>
                            </tr>
                            @if($ticket->latitude && $ticket->longitude)
                            <tr>
                                <td><strong>Location:</strong></td>
                                <td>
                                    <small class="text-muted">
                                        {{ $ticket->latitude }}, {{ $ticket->longitude }}
                                    </small>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6>Subject</h6>
                        <p class="fs-5 fw-semibold">{{ $ticket->subject }}</p>
                        
                        <h6>Description</h6>
                        <div class="border rounded p-3 bg-light">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Log</h5>
            </div>
            <div class="card-body">
                @if($ticket->activities->count() > 0)
                    <div class="timeline">
                        @foreach($ticket->activities->sortByDesc('created_at') as $activity)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary rounded-circle me-3" 
                                     style="width: 12px; height: 12px; margin-top: 5px;"></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong class="text-capitalize">
                                            {{ str_replace('_', ' ', $activity->activity_type) }}
                                        </strong>
                                        <small class="text-muted">
                                             {{ $activity->created_at ? $activity->created_at->format('M j, Y g:i A') : 'N/A' }}
                                        </small>
                                    </div>
                                    <p class="mb-1">{{ $activity->description }}</p>
                                    @if($activity->user)
                                        <small class="text-muted">
                                            By: {{ $activity->user->name }}
                                        </small>
                                    @else
                                        <small class="text-muted">By: System</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3">No activity recorded for this ticket yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Quick Actions -->
        @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" 
                            class="btn btn-outline-primary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#statusModal">
                        <i class="fas fa-sync-alt"></i> Update Status
                    </button>
                    
                    @if($ticket->email)
                    <a href="mailto:{{ $ticket->email }}?subject=Re: {{ $ticket->subject }} - {{ $ticket->ticket_number }}" 
                       class="btn btn-outline-success">
                        <i class="fas fa-envelope"></i> Send Email
                    </a>
                    @endif

                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Tickets
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Ticket Statistics -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Ticket Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Age:</strong></td>
                        <td>{{ $ticket->created_at->diffForHumans() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Activities:</strong></td>
                        <td>{{ $ticket->activities->count() }} records</td>
                    </tr>
                    <tr>
                        <td><strong>Logged By:</strong></td>
                        <td>
                            @if($ticket->loggedBy)
                                {{ $ticket->loggedBy->name }}
                            @else
                                <span class="text-muted">Customer (Self-service)</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Anonymous Link:</strong></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-info" 
                                    onclick="copyToClipboard('{{ route('tickets.anonymous.show', $ticket->ticket_number) }}')">
                                <i class="fas fa-copy"></i> Copy Link
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
@if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Ticket Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('tickets.update-status', $ticket) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="newly_logged" {{ $ticket->status == 'newly_logged' ? 'selected' : '' }}>Newly Logged</option>
                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Updating the status will send an email notification to: {{ $ticket->email }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = event.target;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('btn-outline-info');
        btn.classList.add('btn-success');
        
        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-info');
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link to clipboard');
    });
}
</script>
@endpush

<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: relative;
    z-index: 1;
}

.table-borderless td {
    border: none;
    padding: 0.25rem 0;
}
</style>