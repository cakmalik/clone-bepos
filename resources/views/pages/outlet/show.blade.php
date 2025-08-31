@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Detail Outlet
                    </h2>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="/outlet" class="btn btn-primary"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali</a>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Outlet</h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Kode Outlet</h4>
                                <p>{{ $outlet->code }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Nama Outlet</h4>
                                <p>{{ $outlet->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Nomor Telp</h4>
                                <p>{{ $outlet->phone }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Alamat</h4>
                                <p>{{ $outlet->address }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Deskripsi</h4>
                                <p>{{ $outlet->desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script></script>
@endpush
