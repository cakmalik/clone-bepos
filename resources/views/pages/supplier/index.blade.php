@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        Supplier
                    </h2>
                    <a href="{{ route('supplier.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>&nbsp;
                        Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Supplier</th>
                                        <th>Nama Suppler</th>
                                        <th>Nomor Telp</th>
                                        <th>Alamat</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suppliers as $sp)
                                        <tr>
                                            <td>{{ $sp->code }}</td>
                                            <td>{{ $sp->name }}</td>
                                            <td>{{ $sp->phone }}</td>
                                            <td>{{ $sp->address }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="btn-list">
                                                        <a href="/supplier/{{ $sp->id }}"
                                                            class="btn btn-outline-primary">
                                                            <li class="fas fa-eye"></li>
                                                        </a>
                                                        <a href="{{ route('supplier.edit', $sp->id) }}"
                                                            class="btn btn-outline-dark">
                                                            <li class="fas fa-edit"></li>
                                                        </a>
                                                        <a onclick="SupplierDelete({{ $sp->id }})"
                                                            class="btn btn-outline-danger">
                                                            <li class="fas fa-trash"></li>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
    <script>
        function SupplierDelete(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/supplier') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                window.location.href = '/supplier';
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                window.location.href = '/supplier';
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi di hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
