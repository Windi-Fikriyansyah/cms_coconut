@extends('template.app')
@section('title', 'Detail Sample Request')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Sample</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('sample.index') }}">Samples</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="card-title">Informasi Pemohon</h5>
                <div>
                     <span class="badge bg-secondary">{{ ucfirst($sample->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" value="{{ $sample->name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Name</label>
                        <input type="text" class="form-control" value="{{ $sample->company_name ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Business Description</label>
                        <textarea class="form-control" rows="3" disabled>{{ $sample->business_description ?? '-' }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="text" class="form-control" value="{{ $sample->email }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="text" class="form-control" value="{{ $sample->phone ?? '-' }}" disabled>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($sample->created_at)->format('d F Y H:i') }}" disabled>
                    </div>
                </div>

                <hr>
                <h5 class="card-title my-3">Informasi Pengiriman</h5>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <textarea class="form-control" rows="3" disabled>{{ $sample->address }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Town</label>
                        <input type="text" class="form-control" value="{{ $sample->town }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Country</label>
                        <input type="text" class="form-control" value="{{ $sample->country }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Courier Preference</label>
                        <input type="text" class="form-control" value="{{ $sample->courier ?? '-' }}" disabled>
                    </div>
                 </div>

                <hr>
                <h5 class="card-title my-3">Detail Permintaan</h5>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Products Interested In</label>
                         @php
                            $products = json_decode($sample->products, true);
                            $productList = is_array($products) ? implode("\n", $products) : $sample->products;
                        @endphp
                        <textarea class="form-control" rows="3" disabled>{{ $productList }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Quantity Needed</label>
                        <input type="text" class="form-control" value="{{ $sample->quantity ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <input type="text" class="form-control" value="{{ $sample->payment_method ?? '-' }}" disabled>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Additional Message</label>
                         <textarea class="form-control" rows="3" disabled>{{ $sample->message ?? '-' }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('sample.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
