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
                        {{ $title }}
                    </h2>
                </div>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <a href="/supplier" class="btn btn-primary"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali</a>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-auto ms-auto d-print-none">
                </div>

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Kode Supplier</h4>
                                <p>{{ $supplier->code }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Nama Supplier</h4>
                                <p>{{ $supplier->name }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h4>Nomor Telp</h4>
                                <p>{{ $supplier->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Alamat</h4>
                                <p>{{ $supplier->address }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <h4>Deskripsi</h4>
                                @if ($supplier->desc)
                                    <p>{{ $supplier->desc }}</p>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
