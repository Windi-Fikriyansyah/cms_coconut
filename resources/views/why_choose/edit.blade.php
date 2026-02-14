@extends('template.app')
@section('title', 'Edit Why Choose Section')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Edit why choose Section</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('why_choose.index') }}">Quality Team</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <form action="{{ route('why_choose.update', $section->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Card 1: Section Images --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Section Images</h4>
                <small class="text-muted">Upload gambar untuk background section why_choose.</small>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="form-label">Images <small class="text-muted">(webp only)</small></label>
                    
                    {{-- Existing Images --}}
                    @php
                        $images = json_decode($section->image ?? '[]', true) ?: [];
                    @endphp
                    <div id="existing-images-container" class="mb-3">
                        @foreach($images as $img)
    @php
        $url = is_array($img) ? $img['url'] : $img;
    @endphp

                            <div class="input-group mb-2 existing-image-row">
                                <span class="input-group-text">
                                    <img src="{{ $url }}" style="height:30px">
                                </span>
                                <input type="text" class="form-control" value="{{ $url }}" disabled>
                                {{-- Hidden input to send back if we keep it --}}
                                <input type="hidden" name="existing_images[]" value="{{ $url }}">
                                <button type="button" class="btn btn-danger btn-remove-image" onclick="removeExistingImage(this)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- New Images --}}
                    <div id="section-images-container">
                        {{-- New uploads go here --}}
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-outline-success mt-1" onclick="addImage()">
                        <i class="bi bi-plus-lg"></i> Tambah Gambar Baru
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
                    @foreach($items as $idx => $item)
                        <div class="item-card" id="item-card-{{ $idx }}">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-item" onclick="removeItem({{ $idx }})">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <h6 class="mb-3">Item #{{ $idx + 1 }}</h6>
                            <input type="hidden" name="item_id[{{ $idx }}]" value="{{ $item->id }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Icon (String)</label>
                                    <input type="text" name="item_icon[{{ $idx }}]" class="form-control" value="{{ $item->icon }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="item_display_order[{{ $idx }}]" class="form-control" value="{{ $item->display_order }}">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="item_title[{{ $idx }}]" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="item_description[{{ $idx }}]" class="form-control" rows="3" required>{{ $item->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- No item msg -->
                <div id="no-item-msg" class="text-center text-muted py-3" @if(count($items) > 0) style="display:none" @endif>
                    <em>Belum ada item. Klik "Tambah Item" untuk menambahkan.</em>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('why_choose.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
        let itemIndex = {{ count($items) }};

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
            btn.closest('.image-row').remove();
        }

        function removeExistingImage(btn) {
            // Removes the entire row including the hidden input.
            // When form is submitted, this image path won't be in 'existing_images[]',
            // so controller will delete it from DB and storage.
            btn.closest('.existing-image-row').remove();
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
                    <input type="hidden" name="item_id[${itemIndex}]" value="">
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
    </script>
@endpush
