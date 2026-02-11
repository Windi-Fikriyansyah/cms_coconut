@extends('template.app')
@section('title', 'Tambah Gallery')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Tambah Gallery</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('gallery.index') }}">Gallery</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Card 1: Metadata --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Gallery Metadata</h4>
                <small class="text-muted">Informasi utama untuk halaman gallery.</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle <span class="text-danger">*</span></label>
                        <input type="text" name="subtitle" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Background Image <span class="text-danger">*</span> <small class="text-muted">(webp only)</small></label>
                        <input type="file" name="background_image" class="form-control" accept=".webp" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Gallery Images --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Gallery Images</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                    <i class="bi bi-plus-lg"></i> Tambah Item
                </button>
            </div>
            <div class="card-body">
                <div id="items-container">
                    {{-- Item rows will be added here --}}
                </div>
                <div id="no-item-msg" class="text-center text-muted py-3">
                    <em>Belum ada image. Klik "Tambah Item" untuk menambahkan.</em>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('gallery.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>

    </form>
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .item-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            background: #fafafa;
            position: relative;
        }
        .item-card .btn-remove-item {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
@endpush

@push('js')
    <script>
        let itemIndex = 0;

        function addItem() {
            document.getElementById('no-item-msg').style.display = 'none';

            const html = `
                <div class="item-card" id="item-card-${itemIndex}">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item" onclick="removeItem(${itemIndex})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h6 class="mb-3">Image Item #${itemIndex + 1}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Image (webp)</label>
                            <input type="file" name="gallery_images[${itemIndex}]" class="form-control" accept=".webp">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="img_display_order[${itemIndex}]" class="form-control" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="img_title[${itemIndex}]" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="img_category[${itemIndex}]" class="form-control">
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('items-container').insertAdjacentHTML('beforeend', html);
            itemIndex++;
        }

        function removeItem(idx) {
            const card = document.getElementById('item-card-' + idx);
            if (card) card.remove();

            if (document.querySelectorAll('.item-card').length === 0) {
                document.getElementById('no-item-msg').style.display = 'block';
            }
        }
    </script>
@endpush
