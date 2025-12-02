@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0">Welcome to Support Ticket System</h2>
            </div>
            <div class="card-body text-center">
                <p class="lead">Support ticket management system</p>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Create Support Ticket</h5>
                                <p class="card-text">Submit a new support request</p>
                                <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create Ticket</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Staff Login</h5>
                                <p class="card-text">Access the support portal</p>
                                <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection