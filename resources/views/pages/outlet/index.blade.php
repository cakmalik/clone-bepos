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
                    <h2 class="page-title">
                        Outlet
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    @if ($can_create)
                        <a href="{{ route('outlet.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus me-2"></i> Outlet
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-auto ms-auto d-print-none">
                    @if (Auth()->user()->role->role_name == 'SUPER SUPERADMIN')
                        <div class="btn-list">
                            <a href="/outlet/create" class="btn btn-primary ">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Outlet
                            </a>
                        </div>
                    @endif
                </div>


                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card py-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="data-table" class="table card-table table-vcenter text-nowrap" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Kode Outlet</th>
                                        <th>Nama Outlet</th>
                                        <th>Tipe Outlet</th>
                                        <th>Alamat</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outlets as $outlet)
                                        <tr>
                                            <td>{{ $outlet->code }}</td>
                                            <td>{{ $outlet->name }}</td>
                                            <td>
                                                @if ($outlet->is_main == true)
                                                    <p>Outlet Utama</p>
                                                @elseif($outlet->is_main == false)
                                                    <p class="text-center">-</p>
                                                @endif
                                            </td>
                                            <td>{{ $outlet->address }}</td>
                                            <td>

                                                <div class="btn-group" role="group">
                                                    <div class="btn-list">
                                                        <a href="{{ route('outlet.show', $outlet->id) }}"
                                                            class="btn btn-outline-primary">
                                                            <li class="fas fa-eye"></li>
                                                        </a>

                                                        <a href="{{ route('outlet.edit', $outlet->id) }}"
                                                            class="btn btn-outline-dark">
                                                            <li class="fas fa-edit"></li>
                                                        </a>
                                                        @if (!$outlet->is_main)
                                                            <a onclick="OutletDelete({{ $outlet->id }})"
                                                                class="btn btn-outline-danger">
                                                                <li class="fas fa-trash"></li>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if ($outlet->type == 'resto')
                                                    <a href="{{ route('outlet-table.index', [$outlet->id]) }}"
                                                        class="btn btn-outline-warning">
                                                        <li class="fas fa-chair"></li>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- <div class="row">
                                            <div class="col-xl-12">
                                                <div class="mt-3" style="text-align: right">
                                                    Showing
                                                    {{ $campaigns->firstItem() }}
                                                    to
                                                    {{ $campaigns->lastItem() }}
                                                    of
                                                    {{ $campaigns->total() }}
                                                    entries
                                                    <div style="margin-top: -27px">
                                                        {{ $campaigns->links() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function OutletDelete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/outlet') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                    window.location.href = '/outlet';
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                window.location.href = '/outlet';
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
