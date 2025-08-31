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
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title mb-0">
                        Produk
                    </h2>
                
                    <div class="d-flex gap-2">
                        <div class="btn-group">
                            <a href="{{ route('product.add') }}" class="btn btn-primary d-flex align-items-center">
                                <i class="fa fa-plus me-2"></i> Produk
                            </a>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="visually-hidden">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importProductModal">
                                        <i class="fa fa-box-open me-2 text-primary"></i> Import Produk
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importStockModal">
                                        <i class="fa fa-archive me-2 text-primary"></i> Import Stok Produk
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importProductPriceModal">
                                        <i class="fa fa-money-bill me-2 text-primary"></i> Import Harga Produk
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @include('pages.product.product_all.import-product')
                    @include('pages.product.product_all.import-stock')
                    @include('pages.product.product_all.import-price')
                </div>
                
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @include('product.product_all.create') --}}

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
                        <ul class="nav nav-tabs card-header-tabs" id="productTab" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-product" class="nav-link active" data-bs-toggle="tab">Produk</a>
                            </li>
                            @if (config('app.current_version_product') == 'retail_advance')
                                <li class="nav-item">
                                    <a href="#tabs-bundle" class="nav-link" data-bs-toggle="tab">Produk Paket</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-body py-3">
                        <!-- Bagian sticky: filter -->
                        <div class="sticky-top-section">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="category">Kategori</label>
                                    <select name="product_category_id" id="product_category_id" class="form-select">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach ($productCategory as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="supplier">Supplier</label>
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">-- Semua Supplier --</option>
                                        @foreach ($supplier as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Tabel dengan header sticky -->
                        <div class="table-container">
                            <div class="table-responsive">
                                <table id="dataTable" class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Merk</th>
                                            <th>Kategori</th>
                                            <th>HPP</th>
                                            <th>Harga Jual</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Isi tabel akan diisi oleh DataTables -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#capital_price').mask('#.##0', {
                reverse: true
            });

            $('.form-select').select2();

        });
    </script>

    <script>
        $(function() {

            $(document).on("keyup", '#dataTable_filter input', function() {
                table.draw();
            });

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                scrollX: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.product = $('#dataTable_filter input').val();
                        d.category = $('#product_category_id').val();
                        d.supplier = $('#supplier_id').val();
                        d.is_bundle = $('#productTab .nav-link.active').attr('href') ===
                            "#tabs-bundle" ? 1 : 0;
                    }
                },

                columns: [{
                        className: 'dt-center',
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'product_category',
                        name: 'product_category'
                    },
                    {
                        data: 'capital_price_new',
                        name: 'capital_price_new'
                    },
                    {
                        data: 'product_price',
                        name: 'product_price'
                    },
                    {
                        "className": "dt-center",
                        data: 'action',
                        name: 'action'
                    }

                ]
            });


            $('#product_category_id, #supplier_id').change(function() {
                table.draw();
            });

            $('#productTab a').on('click', function() {
                table.draw();
            });
        });
    </script>

    <script>
        document.getElementById('destination').addEventListener('change', function() {
            const selectedValue = this.value;
            if (selectedValue === 'inventory') {
                document.getElementById('inventoryGroup').style.display = 'block';
                document.getElementById('outletGroup').style.display = 'none';
            } else if (selectedValue === 'outlet') {
                document.getElementById('outletGroup').style.display = 'block';
                document.getElementById('inventoryGroup').style.display = 'none';
            } else {
                document.getElementById('inventoryGroup').style.display = 'none';
                document.getElementById('outletGroup').style.display = 'none';
            }
        });
    </script>

    <script>
        window.onload = function() {
            const form1 = document.getElementById("importProductForm");
            const form2 = document.getElementById("importStockForm");

            if (form1) form1.reset();
            if (form2) form2.reset();
        };
    </script>

        
@endpush
