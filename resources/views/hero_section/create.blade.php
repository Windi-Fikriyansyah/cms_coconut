@extends('template.app')
@section('title', 'Tambah Hero Section')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Hero Section</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hero.index') }}">Hero Section</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Tambah Hero Section</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('hero.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="badge_text">Badge Text</label>
                                <input type="text" class="form-control @error('badge_text') is-invalid @enderror" id="badge_text" name="badge_text" placeholder="Masukkan Badge Text" value="{{ old('badge_text') }}" required>
                                @error('badge_text')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="title">Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Masukkan Title" value="{{ old('title') }}" required>
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="subtitle">Subtitle</label>
                                <textarea class="form-control @error('subtitle') is-invalid @enderror" id="subtitle" name="subtitle" placeholder="Masukkan Subtitle" rows="3" required>{{ old('subtitle') }}</textarea>
                                @error('subtitle')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_text">CTA Text</label>
                                <input type="text" class="form-control @error('cta_text') is-invalid @enderror" id="cta_text" name="cta_text" placeholder="Masukkan CTA Text" value="{{ old('cta_text') }}" required>
                                @error('cta_text')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_link">CTA Link</label>
                                <input type="text" class="form-control @error('cta_link') is-invalid @enderror" id="cta_link" name="cta_link" placeholder="Masukkan CTA Link" value="{{ old('cta_link') }}" required>
                                @error('cta_link')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="background_image">Background Image</label>
                                <input type="file" class="form-control @error('background_image') is-invalid @enderror" id="background_image" name="background_image" accept="image/*">
                                <small class="text-muted">Format: jpg, png, jpeg, gif. Max: 2MB</small>
                                @error('background_image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                            <a href="{{ route('hero.index') }}" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
