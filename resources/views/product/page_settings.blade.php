@extends('template.app')
@section('title', 'Setting Halaman Produk')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Setting Halaman Produk</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Setting Halaman</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('product.page.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <h5 class="mb-3">Hero Section</h5>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="hero_title" class="form-label">Hero Title</label>
                                <input type="text" name="hero_title" id="hero_title" class="form-control" value="{{ old('hero_title', $page->hero_title ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="hero_description" class="form-label">Hero Description</label>
                                <textarea name="hero_description" id="hero_description" rows="3" class="form-control" required>{{ old('hero_description', $page->hero_description ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="hero_image" class="form-label">Hero Image</label>
                                <input type="file" name="hero_image" id="hero_image" class="form-control">
                                @if(isset($page->hero_image))
                                    <div class="mt-2">
                                        <img src="{{ $page->hero_image }}" alt="Hero Preview" style="height: 100px; border-radius: 8px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">CTA Section</h5>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="cta_description" class="form-label">CTA Description</label>
                                <textarea name="cta_description" id="cta_description" rows="3" class="form-control" required>{{ old('cta_description', $page->cta_description ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_whatsapp" class="form-label">CTA WhatsApp Number</label>
                                <input type="text" name="cta_whatsapp" id="cta_whatsapp" class="form-control" value="{{ old('cta_whatsapp', $page->cta_whatsapp ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_whatsapp_label" class="form-label">CTA WhatsApp Label</label>
                                <input type="text" name="cta_whatsapp_label" id="cta_whatsapp_label" class="form-control" value="{{ old('cta_whatsapp_label', $page->cta_whatsapp_label ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_email" class="form-label">CTA Email</label>
                                <input type="email" name="cta_email" id="cta_email" class="form-control" value="{{ old('cta_email', $page->cta_email ?? '') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="cta_email_label" class="form-label">CTA Email Label</label>
                                <input type="text" name="cta_email_label" id="cta_email_label" class="form-control" value="{{ old('cta_email_label', $page->cta_email_label ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('product.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
