@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $title }}</h4>
                <a href="{{ route('complex-query.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Queries
                </a>
            </div>
            <div class="card-body">
                <!-- SQL Query Display -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">SQL Query Executed</h6>
                            </div>
                            <div class="card-body">
                                <code class="sql-query" style="white-space: pre-wrap; font-size: 0.9em;">{{ $query }}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Count -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info py-2">
                            <strong>Results Found:</strong> {{ $results->count() }} 
                            @if($results->count() === 0)
                                - No records match the query criteria
                            @endif
                        </div>
                    </div>
                </div>

                @if($results->count() > 0)
                    <!-- Results Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    @if(isset($results[0]->name))
                                        <!-- For Query 3 (interests count) -->
                                        <th>Interest Name</th>
                                        <th>Person Count</th>
                                    @else
                                        <!-- For person-based queries -->
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date of Birth</th>
                                        <th>Interests Count</th>
                                        <th>Documents Count</th>
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $result)
                                <tr>
                                    @if(isset($result->name))
                                        <!-- Query 3 Results -->
                                        <td>
                                            <strong>{{ $result->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $result->person_count }}</span>
                                        </td>
                                    @else
                                        <!-- Person-based Results -->
                                        <td>{{ $result->id }}</td>
                                        <td>
                                            <strong>{{ $result->first_name }} {{ $result->last_name }}</strong>
                                        </td>
                                        <td>{{ $result->email }}</td>
                                        <td>
                                            @if($result->date_of_birth)
                                                {{ \Carbon\Carbon::parse($result->date_of_birth)->format('M j, Y') }}
                                            @else
                                                <span class="text-muted">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $interestCount = $result->personalInterests->count();
                                            @endphp
                                            <span class="badge bg-info">{{ $interestCount }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $docCount = 0;
                                                foreach ($result->personalInterests as $pi) {
                                                    $docCount += $pi->documents->count();
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $docCount > 0 ? 'success' : 'secondary' }}">
                                                {{ $docCount }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal{{ $result->id }}">
                                                <i class="fas fa-eye"></i> Details
                                            </button>
                                        </td>
                                    @endif
                                </tr>

                                <!-- Detail Modal for Person Results -->
                                @if(!isset($result->name))
                                <div class="modal fade" id="detailModal{{ $result->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ $result->first_name }} {{ $result->last_name }} - Details
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Personal Information</h6>
                                                        <p><strong>Email:</strong> {{ $result->email }}</p>
                                                        <p><strong>Date of Birth:</strong> 
                                                            @if($result->date_of_birth)
                                                                {{ \Carbon\Carbon::parse($result->date_of_birth)->format('F j, Y') }}
                                                            @else
                                                                Not set
                                                            @endif
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Statistics</h6>
                                                        <p><strong>Total Interests:</strong> 
                                                            <span class="badge bg-info">{{ $result->personalInterests->count() }}</span>
                                                        </p>
                                                        <p><strong>Total Documents:</strong> 
                                                            @php
                                                                $totalDocs = 0;
                                                                foreach ($result->personalInterests as $pi) {
                                                                    $totalDocs += $pi->documents->count();
                                                                }
                                                            @endphp
                                                            <span class="badge bg-success">{{ $totalDocs }}</span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <hr>

                                                <h6>Interests & Documents</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Interest</th>
                                                                <th>Category</th>
                                                                <th>Allows Documents</th>
                                                                <th>Documents</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($result->personalInterests as $personalInterest)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $personalInterest->interest->name }}</strong>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-secondary text-capitalize">
                                                                        {{ $personalInterest->interest->category }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($personalInterest->interest->allows_documents)
                                                                        <span class="badge bg-success">Yes</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">No</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($personalInterest->documents->count() > 0)
                                                                        <ul class="list-unstyled mb-0">
                                                                            @foreach($personalInterest->documents as $document)
                                                                            <li>
                                                                                <small>
                                                                                    <i class="fas fa-file"></i> 
                                                                                    {{ $document->file_name }}
                                                                                    <span class="text-muted">
                                                                                        ({{ number_format($document->file_size / 1024, 2) }} KB)
                                                                                    </span>
                                                                                </small>
                                                                            </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        <span class="text-muted">No documents</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Results Summary -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Query Summary:</h6>
                                    <ul class="mb-0">
                                        @if(isset($results[0]->name))
                                            <li>Showing unique interests and person counts without documents</li>
                                            <li>Total unique interests in results: {{ $results->count() }}</li>
                                        @else
                                            <li>Total people matching criteria: {{ $results->count() }}</li>
                                            <li>Click "Details" to view individual interest and document information</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- No Results Message -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Results Found</h4>
                        <p class="text-muted">The query executed successfully but no records match the specified criteria.</p>
                        <a href="{{ route('complex-query.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Queries
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.sql-query {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 15px;
    font-family: 'Courier New', monospace;
    color: #e83e8c;
}

.table th {
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}
</style>
@endsection