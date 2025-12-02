@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Support Tickets</h4>
                @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create New Ticket
                    </a>
                @endif
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Filters & Sorting</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('tickets.index') }}">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" 
                                                   name="start_date" value="{{ request('start_date') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="date" class="form-control" id="end_date" 
                                                   name="end_date" value="{{ request('end_date') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="">All Statuses</option>
                                                <option value="newly_logged" {{ request('status') == 'newly_logged' ? 'selected' : '' }}>New</option>
                                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="order_by" class="form-label">Order By</label>
                                            <select class="form-select" id="order_by" name="order_by">
                                                <option value="created_at" {{ request('order_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Date Logged</option>
                                                <option value="first_name" {{ request('order_by') == 'first_name' ? 'selected' : '' }}>First Name</option>
                                                <option value="last_name" {{ request('order_by') == 'last_name' ? 'selected' : '' }}>Last Name</option>
                                                <option value="status" {{ request('order_by') == 'status' ? 'selected' : '' }}>Status</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="order_direction" class="form-label">Direction</label>
                                            <select class="form-select" id="order_direction" name="order_direction">
                                                <option value="desc" {{ request('order_direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                                                <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter"></i> Apply Filters
                                                </button>
                                                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                                                    <i class="fas fa-times"></i> Clear Filters
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tickets Table -->
                @if($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Customer</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ Str::limit($ticket->subject, 50) }}</span>
                                            <small class="text-muted">{{ Str::limit($ticket->description, 30) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $ticket->first_name }} {{ $ticket->last_name }}</span>
                                            <small class="text-muted">{{ $ticket->email }}</small>
                                            @if($ticket->company)
                                                <small class="text-muted">{{ $ticket->company }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary text-capitalize">{{ $ticket->category }}</span>
                                    </td>
                                    <td>
                                        @if($ticket->priority === 'high')
                                            <span class="badge bg-danger">High</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="badge bg-warning">Medium</span>
                                        @else
                                            <span class="badge bg-info">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($ticket->status === 'newly_logged')
                                            <span class="badge bg-warning">New</span>
                                        @elseif($ticket->status === 'in_progress')
                                            <span class="badge bg-info">In Progress</span>
                                        @else
                                            <span class="badge bg-success">Resolved</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $ticket->created_at->format('M j, Y') }}<br>
                                            <span class="text-muted">{{ $ticket->created_at->format('g:i A') }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tickets.show', $ticket) }}" 
                                               class="btn btn-outline-primary" 
                                               title="View Ticket">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
                                                <button type="button" 
                                                        class="btn btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#statusModal{{ $ticket->id }}"
                                                        title="Update Status">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Status Update Modal -->
                                @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
                                <div class="modal fade" id="statusModal{{ $ticket->id }}" tabindex="-1">
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
                                                        <label for="status{{ $ticket->id }}" class="form-label">Status</label>
                                                        <select class="form-select" id="status{{ $ticket->id }}" name="status" required>
                                                            <option value="newly_logged" {{ $ticket->status == 'newly_logged' ? 'selected' : '' }}>Newly Logged</option>
                                                            <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                        </select>
                                                    </div>
                                                    <div class="alert alert-info">
                                                        <small>
                                                            <i class="fas fa-info-circle"></i>
                                                            Updating the status will send an email notification to the customer.
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0 text-muted">
                                    Showing {{ $tickets->firstItem() }} to {{ $tickets->lastItem() }} of {{ $tickets->total() }} results
                                </p>
                            </div>
                            <nav>
                                {{ $tickets->appends(request()->query())->links() }}
                            </nav>
                        </div>
                    </div>
                @else
                    <!-- No Tickets Found -->
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Tickets Found</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['start_date', 'end_date', 'status', 'order_by']))
                                No tickets match your current filters. Try adjusting your search criteria.
                            @else
                                There are no support tickets in the system yet.
                            @endif
                        </p>
                        @if(auth()->user()->isSupportAgent() || auth()->user()->isAdmin())
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create First Ticket
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}
</style>
@endsection