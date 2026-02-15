@extends('template.app')
@section('title', 'Edit Company Stat')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Company Stat</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('company-stats.index') }}">Company Stats</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('company-stats.update', $stat->id) }}" method="POST">
                    @csrf
                    {{-- Note: we use POST with route() but the controller handles it. 
                         Wait, usually update uses PUT/PATCH. 
                         Let's check the route I defined. 
                         I defined `Route::post('/update/{id}'...)`. 
                         So method="POST" is correct and no @method('PUT') is strictly needed unless I change route.
                         Let's stick to POST as defined in routes. --}}
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="value" class="form-label">Value</label>
                                <input type="text" name="value" id="value" class="form-control" value="{{ $stat->value }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="label" class="form-label">Label</label>
                                <input type="text" name="label" id="label" class="form-control" value="{{ $stat->label }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" name="display_order" id="display_order" class="form-control" value="{{ $stat->display_order }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('company-stats.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
