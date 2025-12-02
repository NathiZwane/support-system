@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Complex Query</h4>
                <form method="POST" action="{{ route('complex-query.generate-data') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-database"></i> Generate Sample Data
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> About This System</h6>
                            <p class="mb-0">This system demonstrates complex database queries with randomly generated data for 50 people, their interests, and associated documents.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Query 1 -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Query 1: Animal Lovers</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Find people who love Animals and have exactly 1 document linked to their animal interest.</p>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>SQL Logic:</strong><br>
                                        JOIN personal_details, personal_interests, interests, documents<br>
                                        WHERE interest = 'Animals'<br>
                                        GROUP BY person HAVING COUNT(documents) = 1
                                    </small>
                                </div>
                                <a href="{{ route('complex-query.query1') }}" class="btn btn-outline-primary btn-sm">
                                    Execute Query
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Query 2 -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">Query 2: Children & Sport Lovers</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Find people who have both Children and Sport interests in their profile.</p>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>SQL Logic:</strong><br>
                                        EXISTS subquery for 'Children' interest<br>
                                        EXISTS subquery for 'Sport' interest<br>
                                        Both conditions must be true
                                    </small>
                                </div>
                                <a href="{{ route('complex-query.query2') }}" class="btn btn-outline-success btn-sm">
                                    Execute Query
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Query 3 -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">Query 3: Unique Interests without Documents</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Show all unique interests and count how many people have each interest without any documents linked.</p>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>SQL Logic:</strong><br>
                                        LEFT JOIN all tables<br>
                                        WHERE documents.id IS NULL<br>
                                        GROUP BY interests with COUNT of people
                                    </small>
                                </div>
                                <a href="{{ route('complex-query.query3') }}" class="btn btn-outline-warning btn-sm">
                                    Execute Query
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Query 4 -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Query 4: Multi-Interest with Documents</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Find people with 5-6 interests where at least one interest has multiple documents associated.</p>
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <strong>SQL Logic:</strong><br>
                                        COUNT DISTINCT interests between 5-6<br>
                                        SUM of documents >= 1<br>
                                        Complex HAVING clause with conditions
                                    </small>
                                </div>
                                <a href="{{ route('complex-query.query4') }}" class="btn btn-outline-info btn-sm">
                                    Execute Query
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Structure Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Data Structure Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>Tables Created:</h6>
                                        <ul class="list-unstyled">
                                            <li><code>personal_details</code> - Person information</li>
                                            <li><code>interests</code> - Available interests (15 types)</li>
                                            <li><code>personal_interests</code> - Person-interest relationships</li>
                                            <li><code>documents</code> - Interest-related documents</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Data Rules:</h6>
                                        <ul class="list-unstyled">
                                            <li>. 50 people generated</li>
                                            <li>. 3-12 interests per person</li>
                                            <li>. 15 different interest types</li>
                                            <li>. 60% document linking rate</li>
                                            <li>. No docs for Sport/Fishing</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Document Rules:</h6>
                                        <ul class="list-unstyled">
                                            <li>. Documents allowed for: Gardening, Animals, Children</li>
                                            <li>. No documents for: Sport, Fishing</li>
                                            <li>. Multiple documents possible for allowed interests</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection