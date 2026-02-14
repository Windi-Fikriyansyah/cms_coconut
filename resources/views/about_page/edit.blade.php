@extends('template.app')
@section('title', 'Edit About Page')

@section('content')
<div class="page-heading">
    <h3>Edit About Page</h3>
</div>

<div class="page-content">
    <form action="{{ route('about-page.update', $about->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Hero Section --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">1. Hero Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Badge</label>
                        <input type="text" name="hero_badge" class="form-control" value="{{ $about->hero_badge }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Title</label>
                        <input type="text" name="hero_title" class="form-control" value="{{ $about->hero_title }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Hero Description</label>
                        <textarea name="hero_description" rows="3" class="form-control" required>{{ $about->hero_description }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Image (WebP only)</label>
                        @if($about->hero_image)
                            <div class="mb-2">
                                <img src="{{$about->hero_image }}" style="height: 100px; border-radius: 8px;">
                            </div>
                        @endif
                        <input type="file" name="hero_image" class="form-control" accept="image/webp">
                    </div>
                </div>
            </div>
        </div>

        {{-- Journey Section --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">2. Our Journey Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Journey Title</label>
                        <input type="text" name="journey_title" class="form-control" value="{{ $about->journey_title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Journey Description 1</label>
                        <textarea name="journey_description_1" rows="4" class="form-control" required>{{ $about->journey_description_1 }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Journey Description 2</label>
                        <textarea name="journey_description_2" rows="4" class="form-control" required>{{ $about->journey_description_2 }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label d-flex justify-content-between">
                            Journey Images (WebP, Max 3)
                            <button type="button" class="btn btn-sm btn-success" id="add-journey-image"><i class="bi bi-plus-circle"></i> Add Image</button>
                        </label>
                        <div id="journey-images-container" class="row">
                            @if(!empty($about->journey_image))
                                @foreach($about->journey_image as $img)
                                    <div class="col-md-4 mb-3 journey-image-item">
                                        <div class="card border p-2 shadow-sm">
                                            <div class="mb-2 text-center">
                                                <img src="{{ $img->url }}" style="height: 80px; border-radius: 8px;">
                                                <input type="hidden" name="existing_journey_images[]" value="{{ $img->url }}">
                                            </div>
                                            <div class="input-group">
                                                <input type="file" name="journey_image_replace[]" class="form-control" accept="image/webp">
                                                <button type="button" class="btn btn-danger remove-existing-journey"><i class="bi bi-trash"></i></button>
                                            </div>
                                            <small class="text-muted mt-1" style="font-size: 0.75rem;">Leave empty to keep current</small>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <small class="text-muted">You can replace existing images or add new ones (up to 3 total).</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Vision & Mission --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">3. Vision & Mission Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Vision Title</label>
                        <input type="text" name="vision_title" class="form-control" value="{{ $about->vision_title }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Vision Description</label>
                        <textarea name="vision_description" rows="3" class="form-control" required>{{ $about->vision_description }}</textarea>
                    </div>
                    <hr>
                    <div class="col-12 mb-3">
                        <label class="form-label">Mission Title</label>
                        <input type="text" name="mission_title" class="form-control" value="{{ $about->mission_title }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label d-flex justify-content-between">
                            Mission Points
                            <button type="button" class="btn btn-sm btn-success" id="add-mission-point"><i class="bi bi-plus-circle"></i> Add Point</button>
                        </label>
                        <div id="mission-points-container">
                            @foreach($about->mission_points as $point)
                            <div class="input-group mb-2">
                                <input type="text" name="mission_points[]" class="form-control" value="{{ $point }}" required>
                                <button type="button" class="btn btn-danger remove-row"><i class="bi bi-trash"></i></button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Core Values --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">4. Core Values Section</h4>
            </div>
            <div class="card-body mt-3">
                <label class="form-label d-flex justify-content-between">
                    Values Data
                    <button type="button" class="btn btn-sm btn-success" id="add-value-item"><i class="bi bi-plus-circle"></i> Add Value</button>
                </label>
                <div id="values-container" class="row">
                    @foreach($about->values_data as $idx => $val)
                    <div class="col-md-6 mb-3 value-item">
                        <div class="card shadow-sm border text-dark">
                            <div class="card-body">
                                <div class="text-end mb-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Icon (Lucide name)</label>
                                    <input type="text" name="values_data[{{ $idx }}][icon]" class="form-control" value="{{ $val->icon }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="values_data[{{ $idx }}][title]" class="form-control" value="{{ $val->title }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Text</label>
                                    <textarea name="values_data[{{ $idx }}][text]" rows="2" class="form-control" required>{{ $val->text }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Commitment Section --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">5. Commitment Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Commitment Title</label>
                        <input type="text" name="commitment_title" class="form-control" value="{{ $about->commitment_title }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Commitment Description</label>
                        <textarea name="commitment_description" rows="3" class="form-control" required>{{ $about->commitment_description }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Commitment Image (WebP)</label>
                        @if($about->commitment_image)
                            <div class="mb-2">
                                <img src="{{ Storage::disk('nextjs')->url(str_replace('/uploads/', '', $about->commitment_image)) }}" style="height: 100px; border-radius: 8px;">
                            </div>
                        @endif
                        <input type="file" name="commitment_image" class="form-control" accept="image/webp">
                    </div>
                </div>
            </div>
        </div>

        {{-- Process Section --}}
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0 text-white">6. Our Process Section</h4>
            </div>
            <div class="card-body mt-3">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Process Title</label>
                        <input type="text" name="process_title" class="form-control" value="{{ $about->process_title }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Process Subtitle</label>
                        <input type="text" name="process_subtitle" class="form-control" value="{{ $about->process_subtitle }}" required>
                    </div>
                </div>
                <hr>
                <label class="form-label d-flex justify-content-between">
                    Process Items
                    <button type="button" class="btn btn-sm btn-success" id="add-process-item"><i class="bi bi-plus-circle"></i> Add Step</button>
                </label>
                <div id="process-container" class="row">
                    @foreach($about->process_items as $idx => $item)
                    <div class="col-md-4 mb-3 process-item">
                        <div class="card shadow-sm border text-dark">
                            <div class="card-body">
                                <div class="text-end mb-2">
                                    <button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button>
                                </div>
                                <div class="mb-2 text-center">
                                    @if(isset($item->image) && $item->image)
                                        <img src="{{ Storage::disk('nextjs')->url(str_replace('/uploads/', '', $item->image)) }}" style="height: 60px; border-radius: 4px; border: 1px solid #ddd;">
                                        <input type="hidden" name="process_items[{{ $idx }}][image]" value="{{ $item->image }}">
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Image (WebP)</label>
                                    <input type="file" name="process_image[{{ $idx }}]" class="form-control" accept="image/webp">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Title</label>
                                    <input type="text" name="process_items[{{ $idx }}][title]" class="form-control" value="{{ $item->title }}" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label">Step Description</label>
                                    <textarea name="process_items[{{ $idx }}][description]" rows="2" class="form-control" required>{{ $item->description }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-12 text-end mb-5">
            <button type="submit" class="btn btn-info btn-lg px-5 text-white">Update Semua Data</button>
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

        // Journey Image logic
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
                <div class="col-md-4 mb-3 journey-image-item">
                    <div class="card border p-2 shadow-sm bg-light">
                        <div class="mb-2">
                             <label class="form-label">New Image</label>
                             <div class="input-group">
                                 <input type="file" name="journey_image_new[]" class="form-control" accept="image/webp" required>
                                 <button type="button" class="btn btn-danger remove-journey-new"><i class="bi bi-trash"></i></button>
                             </div>
                        </div>
                    </div>
                </div>
            `;
            $('#journey-images-container').append(html);
        });

        $(document).on('click', '.remove-existing-journey', function() {
            $(this).closest('.journey-image-item').remove();
        });

        $(document).on('click', '.remove-journey-new', function() {
            $(this).closest('.journey-image-item').remove();
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
        let valueIdx = {{ count($about->values_data) }};
        $('#add-value-item').click(function() {
            const html = `
                <div class="col-md-6 mb-3 value-item">
                    <div class="card shadow-sm border text-dark">
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
        let processIdx = {{ count($about->process_items) }};
        $('#add-process-item').click(function() {
            const html = `
                <div class="col-md-4 mb-3 process-item">
                    <div class="card shadow-sm border text-dark">
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
