@extends('template.app')
@section('title', 'Tambah Contact Section')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Tambah Contact Section</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('contact.index') }}">Contact</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Contact Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Get in Touch">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Subtitle <span class="text-danger">*</span></label>
                        <input type="text" name="subtitle" class="form-control" required placeholder="e.g. Contact Us">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required placeholder="+62...">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="whatsapp" class="form-control" required placeholder="+62...">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Map Embed URL <span class="text-danger">*</span></label>
                        <input type="text" name="map_embed_url" class="form-control" required placeholder="https://www.google.com/maps/embed?pb=...">
                        <small class="text-muted">Copy the embed link from Google Maps (src attribute).</small>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('contact.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
