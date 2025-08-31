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
                        Penerimaan Pembelian
                    </h2>
                    <a href="/purchase_reception/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Penerimaan (PN)
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
                        <div class="row mb-4">
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
                                        <option value="" selected>-- SEMUA --</option>
                                        <option value="Open" >BELUM SELESAI</option>
                                        <option value="Finish">SELESAI</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Kode PN</th>
                                        {{-- <th>Penerima</th> --}}
                                        <th>Tanggal</th>
                                        <th>Dibuat</th>
                                        <th width="5%">Status Penerimaan</th>
                                        <th width="5%">Status Pembayaran</th>
                                        <th></th>
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
@endsection
@push('scripts')
    <script>
        $(function() {

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
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
                        "className": "dt-center",
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    // {
                    //     data: 'inventory.name',
                    //     name: 'inventory.name'
                    // },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date'
                    },
                    {
                        data: 'user.users_name',
                        name: 'user.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
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
    </script>
@endpush
