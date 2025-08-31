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
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Pengaturan Stok Produk
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card py-3">
                        <form action="{{ route('settingProductStock.updateAllStok') }}" method="POST"
                            enctype="multipart/form-data" class="form-detect-unsaved">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        Tentukan Jumlah <b><em>Minimum</em></b> stok produk
                                                    </label>
                                                    <small class="text-danger">
                                                        <em>*Perubahan ini akan mengubah semua data stok
                                                            minimum pada produk</em>
                                                    </small>
                                                    <input class="form-control" name="minimum_stock" type="number"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @include('pages.setting.product_stock.edit')
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
