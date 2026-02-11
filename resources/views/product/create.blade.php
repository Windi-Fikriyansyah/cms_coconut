@extends('template.app')
@section('title', 'Tambah Produk')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Tambah Produk</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('product.index') }}">Data Produk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Card 1: Product Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Informasi Produk</h4>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Image (single, webp only) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image <small class="text-muted">(format: webp)</small></label>
                        <input type="file" name="image"
                               class="form-control @error('image') is-invalid @enderror"
                               accept=".webp">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Short Description <span class="text-danger">*</span></label>
                        <input type="text" name="short_description"
                               class="form-control @error('short_description') is-invalid @enderror"
                               value="{{ old('short_description') }}" required>
                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Why Points (dynamic) --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Why Points</label>
                        <div id="why-points-container">
                            <div class="input-group mb-2 why-point-row">
                                <input type="text" name="why_points[]" class="form-control" placeholder="Contoh: High Stability: Resists oxidation during prolonged heating.">
                                <button type="button" class="btn btn-danger btn-remove-why" onclick="removeWhyPoint(this)">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWhyPoint()">
                            <i class="bi bi-plus-lg"></i> Tambah Why Point
                        </button>
                    </div>

                    {{-- Meta Title --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control"
                               value="{{ old('meta_title') }}" maxlength="255">
                    </div>

                    {{-- Meta Description --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2" maxlength="500">{{ old('meta_description') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        {{-- Card 2: Product Details --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Product Details</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDetail()">
                    <i class="bi bi-plus-lg"></i> Tambah Detail
                </button>
            </div>
            <div class="card-body">
                <div id="details-container">
                    {{-- Detail rows will be added here dynamically --}}
                </div>
                <div id="no-detail-msg" class="text-center text-muted py-3">
                    <em>Belum ada detail. Klik "Tambah Detail" untuk menambahkan.</em>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('product.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>

    </form>
</div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .detail-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            background: #fafafa;
            position: relative;
        }
        .detail-card .btn-remove-detail {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .detail-image-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .detail-image-row .form-control {
            flex: 1;
        }
    </style>
@endpush

@push('js')
    {{-- CKEditor 5 CDN --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
    <script>
        let detailIndex = 0;

        // ===== Why Points =====
        function addWhyPoint() {
            const html = `
                <div class="input-group mb-2 why-point-row">
                    <input type="text" name="why_points[]" class="form-control" placeholder="Contoh: Neutral Profile: Doesn't affect the taste of fried products.">
                    <button type="button" class="btn btn-danger btn-remove-why" onclick="removeWhyPoint(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            document.getElementById('why-points-container').insertAdjacentHTML('beforeend', html);
        }

        function removeWhyPoint(btn) {
            btn.closest('.why-point-row').remove();
        }

        // ===== Product Details =====
        function addDetail() {
            document.getElementById('no-detail-msg').style.display = 'none';

            const html = `
                <div class="detail-card" id="detail-card-${detailIndex}">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-detail" onclick="removeDetail(${detailIndex})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h6 class="mb-3">Detail #${detailIndex + 1}</h6>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="detail_title[${detailIndex}]" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="detail_display_order[${detailIndex}]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description <small class="text-muted">(CKEditor)</small></label>
                            <textarea name="detail_description[${detailIndex}]" id="detail-desc-${detailIndex}" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Images <small class="text-muted">(webp only)</small></label>
                            <div id="detail-images-container-${detailIndex}">
                                <div class="detail-image-row">
                                    <input type="file" name="detail_images[${detailIndex}][]" class="form-control" accept=".webp">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeDetailImage(this)">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="addDetailImage(${detailIndex})">
                                <i class="bi bi-plus-lg"></i> Tambah Gambar
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('details-container').insertAdjacentHTML('beforeend', html);

            // Initialize CKEditor for this detail
            ClassicEditor
                .create(document.querySelector(`#detail-desc-${detailIndex}`), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', 'undo', 'redo']
                })
                .catch(error => console.error(error));

            detailIndex++;
        }

        function removeDetail(idx) {
            const card = document.getElementById('detail-card-' + idx);
            if (card) card.remove();

            // Show "no detail" message if all removed
            if (document.querySelectorAll('.detail-card').length === 0) {
                document.getElementById('no-detail-msg').style.display = 'block';
            }
        }

        // ===== Detail Images =====
        function addDetailImage(detailIdx) {
            const container = document.getElementById('detail-images-container-' + detailIdx);
            const html = `
                <div class="detail-image-row">
                    <input type="file" name="detail_images[${detailIdx}][]" class="form-control" accept=".webp">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeDetailImage(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeDetailImage(btn) {
            const row = btn.closest('.detail-image-row');
            const container = row.parentElement;
            // Keep at least one input
            if (container.querySelectorAll('.detail-image-row').length > 1) {
                row.remove();
            }
        }
    </script>
@endpush
