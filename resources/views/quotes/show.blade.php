@extends('template.app')
@section('title', 'Detail Quote Request')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Quote</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('quote.index') }}">Quotes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="card">
           
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Name</label>
                        <input type="text" class="form-control" value="{{ $quote->name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company Name</label>
                        <input type="text" class="form-control" value="{{ $quote->company_name ?? '-' }}" disabled>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="text" class="form-control" value="{{ $quote->email }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Phone</label>
                        <input type="text" class="form-control" value="{{ $quote->phone ?? '-' }}" disabled>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Date</label>
                        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($quote->created_at)->format('d F Y H:i') }}" disabled>
                    </div>
                </div>

                <hr>
                <h5 class="card-title my-3">Informasi Pengiriman</h5>
                 <div class="row">
                   <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <textarea class="form-control" rows="2" disabled>{{ $quote->address ?? '-' }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Town</label>
                        <input type="text" class="form-control" value="{{ $quote->town ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-bold">Country</label>
                        <input type="text" class="form-control" value="{{ $quote->country ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Incoterms</label>
                        <input type="text" class="form-control" value="{{ $quote->incoterms ?? '-' }}" disabled>
                    </div>
                </div>

                <hr>
                <h5 class="card-title my-3">Detail Permintaan</h5>
                 <div class="row">
                   <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Product of Interest</label>
                        @php
                            $products = json_decode($quote->products, true);
                            $productList = is_array($products) ? implode("\n", $products) : $quote->products;
                        @endphp
                        <textarea class="form-control" rows="3" disabled>{{ $productList }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Quantity Needed</label>
                        <input type="text" class="form-control" value="{{ $quote->quantity ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Packaging Requirement</label>
                        <input type="text" class="form-control" value="{{ $quote->packaging ?? '-' }}" disabled>
                    </div>
                     <div class="col-12">
                        <label class="form-label fw-bold">Additional Message</label>
                        <textarea class="form-control" rows="3" disabled>{{ $quote->message ?? '-' }}</textarea>
                    </div>
               </div>

                <div class="mt-4">
                    <a href="{{ route('quote.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
