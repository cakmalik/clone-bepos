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
                        Gudang
                    </h2>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i>&nbsp;
                        Gudang
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
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label">Tipe</label>
                                <select class="form-select filter-table" id="filter-type">
                                    <option value="" selected>Semua</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type['value'] }}">{{ $type['text'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                                <label class="form-label">Status</label>
                                <select class="form-select filter-table" id="filter-status">
                                    <option value="" selected>Semua</option>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="inventory-table"
                                        class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Kode</th>
                                                <th>Nama</th>
                                                <th>Tipe</th>
                                                <th>Gudang Induk</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let table
        $(document).ready(function() {
            table = $('#inventory-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ url('inventory') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'type': $('#filter-type').val(),
                            'status': $('#filter-status').val(),
                        });
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false,
                        width: '5%'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: row => row.parent?.name || '-',
                        name: 'parent'
                    },
                    {
                        data: row =>
                            row.is_active ?
                            `<span class="badge bg-green-lt">Aktif</span>` :
                            `<span class="badge bg-red-lt">Tidak Aktif</span>`
                    },
                    {
                        data: row => `
                            <a href="{{ url('inventory') }}/${row.id}/edit"
                               class="btn btn-outline-dark ">
                                <li class="fas fa-edit"></li>
                            </a>

                            <a class="btn btn-outline-danger"
                               onclick="deleteInventory(${row.id})">
                                <li class="fas fa-trash"></li>
                            </a>
                        `
                    },
                ]
            })

            $('.filter-table').each(function() {
                $(this).on('change', function() {
                    table.ajax.reload();
                })
            })
        })

        function deleteInventory(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/inventory') }}/" + id + '/destroy',
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                table.ajax.reload();
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error');
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
