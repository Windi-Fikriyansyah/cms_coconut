@extends('template.app')
@section('title', 'Create Footer Setting')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Create Footer Setting</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('footer.index') }}">Footer</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Footer Information</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('footer.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 mb-4">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Enter company footer description..."></textarea>
                    </div>

                    <h5 class="mb-3">Social Media Links</h5>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-linkedin text-primary"></i> LinkedIn URL</label>
                        <input type="url" name="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/...">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-instagram text-danger"></i> Instagram URL</label>
                        <input type="url" name="instagram_url" class="form-control" placeholder="https://instagram.com/...">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-facebook text-primary"></i> Facebook URL</label>
                        <input type="url" name="facebook_url" class="form-control" placeholder="https://facebook.com/...">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-youtube text-danger"></i> YouTube URL</label>
                        <input type="url" name="youtube_url" class="form-control" placeholder="https://youtube.com/@...">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="bi bi-tiktok text-dark"></i> TikTok URL</label>
                        <input type="url" name="tiktok_url" class="form-control" placeholder="https://tiktok.com/@...">
                    </div>
                    
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('footer.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
