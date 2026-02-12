@extends('template.app')
@section('title', 'Tambah Why Choose Section')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Tambah Why Choose Section</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('why_choose.index') }}">Quality Team</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <form action="{{ route('why_choose.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Card 1: Section Images --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Section Images</h4>
                <small class="text-muted">Upload gambar untuk background section why choose.</small>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">Images <small class="text-muted">(webp only)</small></label>
                    
                    <div id="section-images-container">
                        <div class="input-group mb-2 image-row">
                            <input type="file" name="section_images[]" class="form-control" accept=".webp">
                            <button type="button" class="btn btn-danger btn-remove-image" onclick="removeImage(this)">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="addImage()">
                        <i class="bi bi-plus-lg"></i> Tambah Gambar
                    </button>
                    @error('section_images') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Card 2: Quality Items --}}
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Quality Commitment Items</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                    <i class="bi bi-plus-lg"></i> Tambah Item
                </button>
            </div>
            <div class="card-body">
                <div id="items-container">
                    {{-- Item rows will be added here --}}
                </div>
                <div id="no-item-msg" class="text-center text-muted py-3">
                    <em>Belum ada item. Klik "Tambah Item" untuk menambahkan.</em>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('why_choose.index') }}" class="btn btn-secondary">Batal</a>
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

        // ===== Section Images =====
        function addImage() {
            const html = `
                <div class="input-group mb-2 image-row">
                    <input type="file" name="section_images[]" class="form-control" accept=".webp">
                    <button type="button" class="btn btn-danger btn-remove-image" onclick="removeImage(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            `;
            document.getElementById('section-images-container').insertAdjacentHTML('beforeend', html);
        }

        function removeImage(btn) {
            const container = document.getElementById('section-images-container');
            // Keep at least one input if you want, or allow empty?
            // User requested "column image" exists, so usually required?
            // But let's allow removing rows freely as long as the user knows at least one is usually needed.
            // But good UX: keep one if it's the last one? Or just verify on submit?
            // Controller validation says nullable|array. So it's fine.
            if (container.querySelectorAll('.image-row').length > 1) {
                btn.closest('.image-row').remove();
            } else {
                // Clear the value instead of removing row if it's the last one
                btn.closest('.image-row').querySelector('input').value = '';
            }
        }

        // ===== Items =====
        function addItem() {
            document.getElementById('no-item-msg').style.display = 'none';

            const html = `
                <div class="item-card" id="item-card-${itemIndex}">
                    <button type="button" class="btn btn-sm btn-danger btn-remove-item" onclick="removeItem(${itemIndex})">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h6 class="mb-3">Item #${itemIndex + 1}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Icon (String)</label>
                            <input type="text" name="item_icon[${itemIndex}]" class="form-control" placeholder="example: ShieldCheck" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="item_display_order[${itemIndex}]" class="form-control" value="0">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="item_title[${itemIndex}]" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="item_description[${itemIndex}]" class="form-control" rows="3" required></textarea>
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
        
        // Add one image input by default on load if none exists (already in HTML)
        // Add one item on load? User might not want items immediately.
    </script>
@endpush
