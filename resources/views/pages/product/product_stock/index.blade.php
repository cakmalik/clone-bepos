@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Stok Produk
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{--                @include('pages.product.product_stock.create')--}}

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}"/>
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}"/>
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}"/>
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Stok Produk</h3>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('productStock.editAllStock') }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i>
                                Perbarui Stock
                            </a>
                        </div>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3 mb-3">
                                <label class="form-control-label">Tipe Produk</label>
                                <select id="filter-type-product" class="form-control">
                                    <option value="" selected>Semua</option>
                                    @foreach ($type_products as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="products-table"
                                           class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th>Code Product</th>
                                            <th>Name</th>
                                            <th>Tipe</th>
                                            <th>Stock</th>
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
        $(document).ready(function () {
            const table = $('#products-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('productStock.index') }}",
                    data: function (d) {
                        return $.extend({}, d, {
                            'type_product': $('#filter-type-product').val(),
                        })
                    },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: row => row.product.code, name: 'code'},
                    {data: row => row.product.name, name: 'name'},
                    {data: row => row.product.type_product, name: 'type_product'},
                    {data: row => row.stock_current, name: 'stock'},
                ]
            });

            $('#filter-type-product').on('change', function () {
                table.ajax.reload()
            })
        })
    </script>
@endpush
