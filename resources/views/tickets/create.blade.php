@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Create Support Ticket</h4>
            </div>
            <div class="card-body">
                <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name') }}" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name') }}" required maxlength="100">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company" name="company" 
                               value="{{ old('company') }}" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label">Category *</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="sales" {{ old('category') == 'sales' ? 'selected' : '' }}>Sales</option>
                            <option value="accounts" {{ old('category') == 'accounts' ? 'selected' : '' }}>Accounts</option>
                            <option value="it" {{ old('category') == 'it' ? 'selected' : '' }}>IT</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               value="{{ old('subject') }}" required maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="5" required minlength="10">{{ old('description') }}</textarea>
                        <div class="form-text">Please provide detailed information about your issue.</div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="getLocation()">
                            <i class="fas fa-location-arrow"></i> Capture Current Location
                        </button>
                        <input type="hidden" id="gps_coordinates" name="gps_coordinates">
                        <div id="locationStatus" class="form-text"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function getLocation() {
        if (navigator.geolocation) {
            document.getElementById('locationStatus').innerHTML = 
                '<span class="text-warning">Getting location...</span>';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const coords = position.coords.latitude + ',' + position.coords.longitude;
                    document.getElementById('gps_coordinates').value = coords;
                    document.getElementById('locationStatus').innerHTML = 
                        '<span class="text-success">Location captured successfully!</span>';
                },
                function(error) {
                    let message = 'Unable to capture location: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            message += 'User denied the request for Geolocation.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            message += 'Location information is unavailable.';
                            break;
                        case error.TIMEOUT:
                            message += 'The request to get user location timed out.';
                            break;
                        default:
                            message += 'An unknown error occurred.';
                    }
                    document.getElementById('locationStatus').innerHTML = 
                        '<span class="text-danger">' + message + '</span>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        } else {
            document.getElementById('locationStatus').innerHTML = 
                '<span class="text-danger">Geolocation is not supported by this browser.</span>';
        }
    }

    // Form validation
    document.getElementById('ticketForm').addEventListener('submit', function(e) {
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        
        // Basic name validation (letters and spaces only)
        const nameRegex = /^[a-zA-Z\s]+$/;
        if (!nameRegex.test(firstName) || !nameRegex.test(lastName)) {
            e.preventDefault();
            alert('Names can only contain letters and spaces.');
            return false;
        }
    });
</script>
@endpush
@endsection