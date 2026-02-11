@extends('template.app')
@section('title', 'Tambah About Section')

@section('content')
<div class="page-heading">
    <h3>Tambah About Section</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Form Tambah About Section</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('about.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    {{-- title --}}
                    <div class="col-md-6 mb-3">
                        <label>Title</label>
                        <input type="text" name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                    </div>

                    {{-- description --}}
                    <div class="col-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" rows="6"
                                  class="form-control @error('description') is-invalid @enderror"
                                  required>{{ old('description') }}</textarea>
                    </div>

                    {{-- 3 separate images --}}
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="col-md-4 mb-3">
                            <label>Image {{ $i }}</label>
                            <input type="file" name="image{{ $i }}" class="form-control @error('image'.$i) is-invalid @enderror" accept="image/*">
                            @error('image'.$i)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endfor

                    {{-- button --}}
                    <div class="col-md-6 mb-3">
                        <label>Button Text</label>
                        <input type="text" name="button_text" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Button Link</label>
                        <input type="text" name="button_link" class="form-control">
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Simpan</button>
                        <a href="{{ route('about.index') }}" class="btn btn-secondary">Batal</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
