@extends('template.app')
@section('title', 'Edit About Section')

@section('content')
<div class="page-heading">
    <h3>Edit About Section</h3>
</div>

<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4>Form Edit About Section</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('about.update', $about->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">

                    {{-- title --}}
                    <div class="col-md-6 mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $about->title) }}" required>
                    </div>

                    {{-- description --}}
                    <div class="col-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" rows="6" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $about->description) }}</textarea>
                    </div>

                    {{-- edit images --}}
                    @php
                        $images = json_decode($about->image ?? '[]', true);
                    @endphp

                    @for ($i = 0; $i < 3; $i++)
                        <div class="col-md-4 mb-3">
                            <label>Image {{ $i+1 }}</label>
                            @if(isset($images[$i]))
                                <div class="mb-2">
                                    <img src="{{ is_array($images[$i]) ? $images[$i]['url'] : $images[$i] }}"
                                         style="height:80px;margin-right:6px;border-radius:6px;">
                                    <small class="d-block text-muted">Gambar saat ini</small>
                                </div>
                            @endif
                            <input type="file" name="image{{ $i+1 }}" class="form-control @error('image'.($i+1)) is-invalid @enderror" accept="image/*">
                        </div>
                    @endfor

                    {{-- button --}}
                    <div class="col-md-6 mb-3">
                        <label>Button Text</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $about->button_text) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Button Link</label>
                        <input type="text" name="button_link" class="form-control" value="{{ old('button_link', $about->button_link) }}">
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('about.index') }}" class="btn btn-secondary">Batal</a>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection
