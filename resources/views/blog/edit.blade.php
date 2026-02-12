@extends('template.app')
@section('title', 'Edit Blog Post')

@section('content')
<div class="page-heading">
    <div class="row">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <h3>Edit Blog Post</h3>
        </div>
        <div class="col-12 col-md-6 order-md-2 order-first">
            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Edit Post Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('blog.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ $post->title }}" required oninput="updateSlug()">
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug" class="form-control" value="{{ $post->slug }}" required readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Author <span class="text-danger">*</span></label>
                        <input type="text" name="author" class="form-control" value="{{ $post->author }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date_str" class="form-control" value="{{ $post->date_str }}" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Tags</label>
                        <input type="text" name="tags" class="form-control" value="{{ $post->tags_string }}" placeholder="e.g. Coconut, Export">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Main Image <small class="text-muted">(webp only)</small></label>
                        @if($post->image)
                            <div class="mb-2">
                                <img src="{{ Storage::disk('nextjs')->url(str_replace('/uploads/', '', $post->image)) }}" style="height:100px;border-radius:8px;">
                                <small class="d-block text-muted">Current Image</small>
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control" accept=".webp">
                        <small class="text-muted">Upload to replace existing image.</small>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Excerpt <span class="text-danger">*</span></label>
                        <textarea name="excerpt" class="form-control" rows="3" required>{{ $post->excerpt }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" class="form-control ckeditor-init" rows="10">{{ $post->content }}</textarea>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('blog.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
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
</script>
@endpush
