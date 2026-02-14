@extends('template.app')
@section('title', 'Detail Message')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Message</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('message.index') }}">Messages</a></li>
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
                        <input type="text" class="form-control" value="{{ $message->name }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Company</label>
                        <input type="text" class="form-control" value="{{ $message->company ?? '-' }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="text" class="form-control" value="{{ $message->email }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <input type="text" class="form-control" value="{{ ucfirst($message->status) }}" disabled>
                    </div>
                    <div class="col-md-12 mb-3">
                         <label class="form-label fw-bold">Date</label>
                         <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($message->created_at)->format('d F Y H:i') }}" disabled>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Message</label>
                        <textarea class="form-control" rows="5" disabled>{{ $message->message }}</textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('message.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
@endsection
