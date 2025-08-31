@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .sticky-top-section {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #fff;
            padding-bottom: 10px;
        }

        .table-container {
            position: relative;
        }

        .table-responsive {
            max-height: 46em;
            overflow-y: auto;
            overflow-x: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            position: sticky;
            top: 0; /* Sesuaikan jika ada elemen lain di atasnya */
            z-index: 5; /* Di bawah filter tapi di atas tbody */
            background-color: #fff;
        }

        thead th {
            border-bottom: 2px solid #dee2e6;
        }

        /* Tidak perlu display: block pada tbody */
        tbody {
            /* Biarkan default agar DataTables merender dengan benar */
        }
    </style>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Stok Outlet
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-stok-7" class="nav-link active" data-bs-toggle="tab">Stok</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-print-7" class="nav-link" data-bs-toggle="tab">Cetak</a>
                            </li>
                        </ul>

                        <a href="{{ route('export.excel', ['type' => 'outlet']) }}" class="btn btn-success">
                            <i class="fa fa-file-excel me-2"></i> Export
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-stok-7">
                                <div class="card-body py-3">
                                    <div class="sticky-top-section">
                                    <div class="row mb-3">
                                        <div class="col-md-4 mb-3">
                                            <select name="inventory_id" id="inventory_id" class="form-select">
                                                @foreach ($inventory as $inv)
                                                    <option value="{{ $inv->id }}">
                                                        {{ $inv->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="col-md-4 mb-3">
                                            <input type="date" name="history_date" class="form-control" id="historyDate" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                        </div> --}}
                                        <div class="col-md-4">
                                            <select name="qty" id="qty" class="form-select">
                                                <option value="1" selected> QTY > 1 </option>
                                                <option value="0"> QTY = 0 </option>
                                                <option value="-"> Semua QTY </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-container">
                                    <div class="table-responsive">
                                        <table
                                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                            id="dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Barcode</th>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th>Qty</th>
                                                    <th>Satuan Lainnya</th>
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
                            <div class="tab-pane" id="tabs-print-7">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <button class="btn btn-primary" onclick="printPageArea()">
                                            <i class="fa fa-print me-2"></i>
                                            Cetak
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="loadingProgress"></div>
                                        <iframe id="printframe" name="printframe"
                                            style="width: 100%; height: 1500px; border:none;"></iframe>
                                    </div>
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
       

        function preview() {
            let app_url = {!! json_encode(url('/')) !!};
            let inventory_id = $('#inventory_id').val();
            let history_date = $('#historyDate').val();
            let qty = $('#qty').val();
            let product = $('#dataTable_filter input').val();

            let urlReport = app_url + '/report/inventory/stock_outlet/print?inventory_id=' + inventory_id +
                '&history_date=' + history_date +
                '&qty=' + qty +
                '&product=' + product;

            $(function() {
                $('#printframe').attr('src', urlReport);
            });
        }



        $(function() {
            preview();
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                scrollX: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.inventory_id = $('#inventory_id').val();

                        d.qty = $('#qty').val();
                        d.product = $('#dataTable_filter input').val();
                    }
                },
                columns: [{
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'product_category',
                        name: 'product_category'
                    },
                    {
                        data: 'stock_current',
                        name: 'stock_current'
                    },
                    {
                        data: 'product_unit_preview',
                        name: 'product_unit_preview'
                    }
                ]
            });
            $('#inventory_id, #historyDate, #qty, #dataTable_filter input').change(function() {
                table.draw();
                preview();
            });
        });

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
