@extends('template.app')
@section('title', 'Footer Settings')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Footer Settings</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Footer</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <i class="bx bx-x-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">List Footer Settings</h5>
                    @if(isset($count) && $count == 0)
                        <a href="{{ route('footer.create') }}" class="btn btn-primary">Setting Footer</a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="footer-table" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Description</th>
                                <th>Created At</th>
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
            const table = $('#footer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('footer.index') }}",
                columns: [
                    { data: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'description', name: 'description' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', orderable: false, searchable: false }
                ],
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
                                    setTimeout(() => location.reload(), 1000); 
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
