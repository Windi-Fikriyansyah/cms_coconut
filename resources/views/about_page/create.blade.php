@extends('template.app')
@section('title', 'Tambah About Page')

@section('content')
<div class="page-heading">
    <h3>Tambah About Page</h3>
</div>

<div class="page-content">
    <form action="{{ route('about-page.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Hero Section --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">1. Hero Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Badge</label>
                        <input type="text" name="hero_badge" class="form-control" value="{{ old('hero_badge') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" class="form-control" value="{{ old('hero_title') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Hero Description</label>
                        <textarea name="hero_description" rows="3" class="form-control" required>{{ old('hero_description') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Image (WebP only)</label>
                        <input type="file" name="hero_image" class="form-control" accept="image/webp">
                    </div>
                </div>
            </div>
        </div>

        {{-- Journey Section --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">2. Our Journey Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Journey Title</label>
                        <input type="text" name="journey_title" class="form-control" value="{{ old('journey_title') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Journey Description 1</label>
                        <textarea name="journey_description_1" rows="4" class="form-control" required>{{ old('journey_description_1') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Journey Description 2</label>
                        <textarea name="journey_description_2" rows="4" class="form-control" required>{{ old('journey_description_2') }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label d-flex justify-content-between">
                            Journey Images (WebP, Max 3)
                            <button type="button" class="btn btn-sm btn-success" id="add-journey-image"><i class="bi bi-plus-circle"></i> Add Image</button>
                        </label>
                        <div id="journey-images-container" class="row">
                            <div class="col-md-4 mb-2 journey-image-item">
                                <div class="input-group">
                                    <input type="file" name="journey_image[]" class="form-control" accept="image/webp" required>
                                    <button type="button" class="btn btn-danger remove-row"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Upload images one by one. Maximum 3 images.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vision & Mission --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">3. Vision & Mission Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Vision Title</label>
                        <input type="text" name="vision_title" class="form-control" value="{{ old('vision_title') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Vision Description</label>
                        <textarea name="vision_description" rows="3" class="form-control" required>{{ old('vision_description') }}</textarea>
                    </div>
                    <hr>
                    <div class="col-12 mb-3">
                        <label class="form-label">Mission Title</label>
                        <input type="text" name="mission_title" class="form-control" value="{{ old('mission_title') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label d-flex justify-content-between">
                            Mission Points
                            <button type="button" class="btn btn-sm btn-success" id="add-mission-point"><i class="bi bi-plus-circle"></i> Add Point</button>
                        </label>
                        <div id="mission-points-container">
                            <div class="input-group mb-2">
                                <input type="text" name="mission_points[]" class="form-control" placeholder="Enter mission point" required>
                                <button type="button" class="btn btn-danger remove-row"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Core Values --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">4. Core Values Section</h4>
            </div>
            <div class="card-body mt-3">
                <label class="form-label d-flex justify-content-between">
                    Values Data
                    <button type="button" class="btn btn-sm btn-success" id="add-value-item"><i class="bi bi-plus-circle"></i> Add Value</button>
                </label>
                <div id="values-container" class="row">
                    <div class="col-md-6 mb-3 value-item">
                        <div class="card shadow-sm border">
                            <div class="card-body">
                                <div class="text-end mb-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Icon (e.g., ShieldCheck, Heart)</label>
                                    <input type="text" name="values_data[0][icon]" class="form-control" placeholder="Lucide icon name" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="values_data[0][title]" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Text</label>
                                    <textarea name="values_data[0][text]" rows="2" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Commitment Section --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">5. Commitment Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Commitment Title</label>
                        <input type="text" name="commitment_title" class="form-control" value="{{ old('commitment_title') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Commitment Description</label>
                        <textarea name="commitment_description" rows="3" class="form-control" required>{{ old('commitment_description') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Commitment Image (WebP)</label>
                        <input type="file" name="commitment_image" class="form-control" accept="image/webp">
                    </div>
                </div>
            </div>
        </div>

        {{-- Process Section --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0 text-white">6. Our Process Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Process Title</label>
                        <input type="text" name="process_title" class="form-control" value="{{ old('process_title') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Process Subtitle</label>
                        <input type="text" name="process_subtitle" class="form-control" value="{{ old('process_subtitle') }}" required>
                    </div>
                </div>
                <hr>
                <label class="form-label d-flex justify-content-between">
                    Process Items
                    <button type="button" class="btn btn-sm btn-success" id="add-process-item"><i class="bi bi-plus-circle"></i> Add Step</button>
                </label>
                <div id="process-container" class="row">
                    <div class="col-md-4 mb-3 process-item">
                        <div class="card shadow-sm border">
                            <div class="card-body">
                                <div class="text-end mb-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Image (WebP)</label>
                                    <input type="file" name="process_image[0]" class="form-control" accept="image/webp">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Title</label>
                                    <input type="text" name="process_items[0][title]" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Description</label>
                                    <textarea name="process_items[0][description]" rows="2" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5">Simpan Semua Data</button>
            <a href="{{ route('about-page.index') }}" class="btn btn-secondary btn-lg px-5">Batal</a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        // Remove row helper
        $(document).on('click', '.remove-row', function() {
            $(this).closest('.input-group, .value-item, .process-item').remove();
        });

        // Add Journey Image
        $('#add-journey-image').click(function() {
            const count = $('.journey-image-item').length;
            if (count >= 3) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Reached',
                    text: 'Maximum 3 images allowed for Journey section.'
                });
                return;
            }
            const html = `
                <div class="col-md-4 mb-2 journey-image-item">
                    <div class="input-group">
                        <input type="file" name="journey_image[]" class="form-control" accept="image/webp" required>
                        <button type="button" class="btn btn-danger remove-row"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            `;
            $('#journey-images-container').append(html);
        });

        // Add Mission Point
        $('#add-mission-point').click(function() {
            const html = `
                <div class="input-group mb-2">
                    <input type="text" name="mission_points[]" class="form-control" placeholder="Enter mission point" required>
                    <button type="button" class="btn btn-danger remove-row"><i class="bi bi-trash"></i></button>
                </div>
            `;
            $('#mission-points-container').append(html);
        });

        // Add Value Item
        let valueIdx = 1;
        $('#add-value-item').click(function() {
            const html = `
                <div class="col-md-6 mb-3 value-item">
                    <div class="card shadow-sm border">
                        <div class="card-body">
                            <div class="text-end mb-2">
                                <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Icon</label>
                                <input type="text" name="values_data[${valueIdx}][icon]" class="form-control" placeholder="Lucide icon name" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Title</label>
                                <input type="text" name="values_data[${valueIdx}][title]" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Text</label>
                                <textarea name="values_data[${valueIdx}][text]" rows="2" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#values-container').append(html);
            valueIdx++;
        });

        // Add Process Item
        let processIdx = 1;
        $('#add-process-item').click(function() {
            const html = `
                <div class="col-md-4 mb-3 process-item">
                    <div class="card shadow-sm border">
                        <div class="card-body">
                            <div class="text-end mb-2">
                                <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Step Image (WebP)</label>
                                <input type="file" name="process_image[${processIdx}]" class="form-control" accept="image/webp">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Step Title</label>
                                <input type="text" name="process_items[${processIdx}][title]" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Step Description</label>
                                <textarea name="process_items[${processIdx}][description]" rows="2" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#process-container').append(html);
            processIdx++;
        });
    });
</script>
@endpush
