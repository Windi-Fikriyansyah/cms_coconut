@extends('template.app')
@section('title', 'Create Blog Post')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Create Blog Post</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    
    {{-- AI Generation Card --}}
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white"><i class="bi bi-robot"></i> AI Content Generator (Gemini)</h5>
        </div>
        <div class="card-body py-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Google Gemini API Key</label>
                    <input type="password" id="gemini_api_key" class="form-control" placeholder="Enter your API Key here...">
                    <small class="text-muted">Key is not saved, only used for this session.</small>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-warning w-100" id="btn-generate" onclick="generateContent()">
                        <i class="bi bi-stars"></i> Generate Content from Title
                    </button>
                </div>
            </div>
            <div id="generation-status" class="mt-2 text-info" style="display:none;">
                <i class="spinner-border spinner-border-sm"></i> Generating content... Please wait.
            </div>
        </div>
    </div>

    {{-- Main Form --}}
    <div class="card">
        <div class="card-header">
            <h4>Post Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required placeholder="Enter blog title (Topic)" oninput="updateSlug()">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control" required readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Author <span class="text-danger">*</span></label>
                        <input type="text" name="author" class="form-control" required value="{{ auth()->user()->name ?? 'Admin' }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date_str" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" id="tags" class="form-control" placeholder="e.g. Coconut, Export, VCO (comma separated)">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Main Image <span class="text-danger">*</span> <small class="text-muted">(webp only)</small></label>
                        <input type="file" name="image" class="form-control" accept=".webp" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Excerpt <span class="text-danger">*</span></label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" class="form-control ckeditor-init" rows="10"></textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('blog.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Init CKEditor if not auto-inited by class
    if(typeof CKEDITOR !== 'undefined') {
        CKEDITOR.config.versionCheck = false; // Disable security warning banner
        CKEDITOR.replace('content');
    }

    function updateSlug() {
        const title = document.getElementById('title').value;
        const slug = title.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        document.getElementById('slug').value = slug;
    }

    // Load API Key from LocalStorage
    document.addEventListener('DOMContentLoaded', function() {
        const savedKey = localStorage.getItem('gemini_api_key');
        if(savedKey) {
            document.getElementById('gemini_api_key').value = savedKey;
        }
    });

    // Save key when generating
    function generateContent() {
        const apiKey = document.getElementById('gemini_api_key').value;
        const title = document.getElementById('title').value;

        if (!apiKey) {
            Swal.fire('Error', 'Please enter Gemini API Key', 'error');
            return;
        }
        
        // Save to LocalStorage
        localStorage.setItem('gemini_api_key', apiKey);
        if (!title) {
            Swal.fire('Error', 'Please enter a Title first', 'error');
            return;
        }

        $('#generation-status').show();
        $('#btn-generate').prop('disabled', true);

        $.ajax({
            url: "{{ route('blog.generate') }}",
            type: "POST",
            data: {
                api_key: apiKey,
                title: title,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $('#generation-status').hide();
                $('#btn-generate').prop('disabled', false);

                if (response.success) {
                    const data = response.data;
                    
                    // Populate Excerpt
                    if(data.excerpt) $('#excerpt').val(data.excerpt);
                    
                    // Populate Tags
                    if(data.tags && Array.isArray(data.tags)) {
                        $('#tags').val(data.tags.join(', '));
                    }

                    // Populate Content (CKEditor)
                    if(data.content) {
                        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances['content']) {
                            CKEDITOR.instances['content'].setData(data.content);
                        } else {
                            // Fallback if ckeditor not active
                            $('#content').val(data.content);
                        }
                    }

                    Swal.fire('Success', 'Content generated successfully!', 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                $('#generation-status').hide();
                $('#btn-generate').prop('disabled', false);
                Swal.fire('Error', 'Request failed: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
            }
        });
    }
</script>
@endpush
