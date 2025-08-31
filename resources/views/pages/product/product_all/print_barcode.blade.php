@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Barcode Produk
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <livewire:product.cetak-barcode />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
