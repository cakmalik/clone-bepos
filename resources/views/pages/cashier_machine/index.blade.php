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
                <div class="col">
                    <h2 class="page-title text-uppercase">
                        Mesin Kasir
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('cashier_machine.create') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Mesin Kasir
                        </a>
                    </div>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> Mesin Kasir</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive text-capitalize">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama mesin kasir</th>
                                        <th>Outlet</th>
                                        <th>Aksi</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($machine as $i)
                                        <tr>
                                            <td>{{ $i->name }}</td>
                                            <td>{{ $i->outlet->name }}</td>
                                            <td>
                                                <a href="{{ route('cashier_machine.edit', $i->id) }}"
                                                    class="btn btn-success">
                                                    <li class="fas fa-edit"></li>
                                                </a>
                                                <a onclick="CashierMachineDelete({{ $i->id }})"
                                                    class="btn btn-danger">
                                                    <li class="fas fa-trash"></li>
                                                </a>
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
        function CashierMachineDelete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/cashier_machine') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                    window.location.href = '/cashier_machine';
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                window.location.href = '/cashier_machine';
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
