@extends('template.app')
@section('title', 'Edit Testimoni')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Testimoni</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('testimoni.index') }}">Testimoni</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('testimoni.update', $testimoni->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $testimoni->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" name="role" id="role" class="form-control" value="{{ $testimoni->role }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="rating" class="form-label">Rating (1-5)</label>
                                <select name="rating" id="rating" class="form-control" required>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ $testimoni->rating == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="display_order" class="form-label">Display Order</label>
                                <input type="number" name="display_order" id="display_order" class="form-control" value="{{ $testimoni->display_order }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="image" class="form-label">Change Image</label>
                                <input type="file" name="image" id="image" class="form-control">
                                @if($testimoni->image)
                                    <div class="mt-2 text-center">
                                        <img src="{{ $testimoni->image }}" alt="Preview" style="height: 100px; border-radius: 8px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="content" class="form-label">Testimonial Content</label>
                                <textarea name="content" id="content" rows="4" class="form-control" required>{{ $testimoni->content }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('testimoni.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
