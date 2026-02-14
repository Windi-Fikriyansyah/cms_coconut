@extends('template.app')
@section('title', 'Tambah Team Member')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Team Member</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('team.index') }}">Our Team</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('team.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" name="role" id="role" class="form-control" placeholder="e.g. Founder & CEO" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="linkedin_url" class="form-label">LinkedIn URL (optional)</label>
                                <input type="url" name="linkedin_url" id="linkedin_url" class="form-control" placeholder="https://linkedin.com/in/username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="instagram_url" class="form-label">Instagram URL (optional)</label>
                                <input type="url" name="instagram_url" id="instagram_url" class="form-control" placeholder="https://instagram.com/username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" name="display_order" id="display_order" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="image" class="form-label">Profile Image (webp recommended)</label>
                                <input type="file" name="image" id="image" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('team.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
