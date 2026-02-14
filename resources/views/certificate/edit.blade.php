@extends('template.app')
@section('title', 'Edit Certificate')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Edit Certificate</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('certificate.index') }}">Certificates</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Edit Certificate</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('certificate.update', $certificate->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $certificate->title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="display_order" class="form-control" value="{{ $certificate->display_order }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Logo Image <small class="text-muted">(webp only)</small></label>
                        @if($certificate->logo)
                            <div class="mb-2">
                                <img src="{{ $certificate->logo }}" style="height:80px;border-radius:6px;border:1px solid #eee;padding:4px;">
                                <small class="d-block text-muted">Current Logo</small>
                            </div>
                        @endif
                        <input type="file" name="logo" class="form-control" accept=".webp">
                        <small class="text-muted">Upload to replace existing logo.</small>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('certificate.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
