@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">File Manipulation</h4>
            </div>
            <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Instructions</h6>
                            <p class="mb-2">Upload a CSV, TXT, or XLSX file containing a single column of string data.</p>
                            <p class="mb-0">The system will deduplicate the data and output an CSV file sorted by your chosen order.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('file-manipulation.process') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="file" class="form-label">Select File *</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" 
                               id="file" name="file" accept=".csv,.txt,.xlsx" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Supported formats: CSV, TXT, XLSX (Max: 10MB)
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Sort Order *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="order_by" 
                                   id="alphabetical" value="alphabetical" checked>
                            <label class="form-check-label" for="alphabetical">
                                Alphabetical Order (A-Z)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="order_by" 
                                   id="length" value="length">
                            <label class="form-check-label" for="length">
                                String Length (Shortest to Longest)
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Process File
                        </button>
                    </div>
                </form>

                <!-- Example File Format -->
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Example File Format</h6>
                            </div>
                            <div class="card-body">
                                <p>Your file should contain a single column of string data:</p>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6>CSV/TXT Example:</h6>
                                        <code class="d-block p-2 bg-light">
                                            Apple<br>
                                            Banana<br>
                                            Cherry<br>
                                            Apple<br>
                                            Date<br>
                                            Banana
                                        </code>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>After Deduplication:</h6>
                                        <code class="d-block p-2 bg-light">
                                            Apple<br>
                                            Banana<br>
                                            Cherry<br>
                                            Date
                                        </code>
                                    </div>
                                    <div class="col-md-4">
                                        <h6>Alphabetical Output:</h6>
                                        <code class="d-block p-2 bg-light">
                                            Apple<br>
                                            Banana<br>
                                            Cherry<br>
                                            Date
                                        </code>
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