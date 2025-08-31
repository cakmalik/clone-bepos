@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />

    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" />

    <style>
        .custom-table {
            margin-top: 0px;
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table th,
        .custom-table td {
            text-align: left;
            padding: 8px;
        }

        .custom-table tr:nth-child(even) {
            background-color: #f2f2f2
        }

        .custom-table th {
            background-color: #1e293b;
            color: white;
        }
    </style>
@endpush

@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        Retur Penjualan
                    </h2>
                    <a href="/retur-sales/create" class="btn btn-primary">
                        <i class="fa-solid fa-plus me-2"></i> Retur Penjualan
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
                    <div class="card-body">
                        <div class="row mb-3">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="success">SUKSES</option>
                                        <option value="draft">DRAFT</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div>
                            <table id="dataTableReturSales"
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Retur</th>
                                        <th>Referensi Penjualan</th>
                                        <th>Tanggal</th>
                                        <th>Nama Outlet</th>
                                        <th>Dibuat</th>
                                        <th>Total Retur</th>
                                        <th>Status</th>
                                        <th>Action</th>
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

    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-detail-content">
                        {{-- table product retur --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {

            var table = $('#dataTableReturSales').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                pageLength: 25,
                destroy: true,

                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },

                columns: [{
                        data: 'sale_code',
                        name: 'sale_code'
                    },
                    {
                        data: 'ref_code',
                        name: 'ref_code'
                    },
                    {
                        data: 'sale_date',
                        name: 'sale_date'
                    },
                    {
                        data: 'outlet.name',
                        name: 'outlet.name'
                    },
                    {
                        data: 'user.users_name',
                        name: 'user.users_name'
                    },
                    {
                        data: 'total_retur',
                        name: 'total_retur'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                    },

                ]
            });

            $('#status').change(function() {
                table.draw();
            });

            $('#start_date').change(function() {
                table.draw();
            });

            $('#end_date').change(function() {
                table.draw();
            });
        });

        $(document).on('click', '#btn-detail', function() {
            var returId = $(this).data('id');

            $.ajax({
                url: '/retur-sales/' + returId + '/detail',
                type: 'GET',
                success: function(response) {
                    $('#modal-detail-content').html(response);
                    $('#detailModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan: ', error);
                    $('#modal-detail-content').html('Terjadi kesalahan dalam memuat data.');
                }
            });
        });
    </script>
@endpush
