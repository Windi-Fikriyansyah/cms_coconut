@extends('template.app')
@section('title', 'Our Team')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Our Team</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Our Team</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Metadata Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Team Header</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('team.metadata.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="{{ $metadata->title ?? '' }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="subtitle" class="form-label">Subtitle</label>
                                <input type="text" name="subtitle" id="subtitle" class="form-control" value="{{ $metadata->subtitle ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update Header</button>
                </form>
            </div>
        </div>

        <!-- DataTable Section -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">List Team Members</h5>
                <a href="{{ route('team.create') }}" class="btn btn-primary">Tambah Member</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle" id="team-table" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Socials</th>
                                <th>Order</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            const table = $('#team-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('team.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'role', name: 'role' },
                    { data: 'socials', name: 'socials', orderable: false, searchable: false },
                    { data: 'display_order', name: 'display_order' },
                    { data: 'action', orderable: false, searchable: false }
                ]
            });

            $(document).on('click', '.delete-btn', function() {
                const url = $(this).data('url');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('Sukses', response.message, 'success');
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal', xhr.responseJSON?.message || 'Error', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
