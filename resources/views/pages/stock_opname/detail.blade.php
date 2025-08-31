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
                        Detail Stock Opname
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> Detail Stok Opname</h3>
                        @if ($stockOpname->status == 'belum_selesai')
                            <a href="{{ route('stockOpname.edit', $stockOpname->id) }}" style="margin-left:80%"
                                class="btn btn-secondary btn-sm py-2 px-3">
                                EDIT
                            </a>
                        @endif
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-md-3">
                                <span>Kode Opname </span>
                                <h3>{{ $stockOpname->code }}</h3>
                            </div>
                            @if ($stockOpname->inventory_id)
                                <div class="col-md-3">
                                    <span>Nama Gudang </span>
                                    <h3>{{ Str::upper($stockOpname->inventory->name) }}</h3>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <span>Nama Outlet </span>
                                    <h3>{{ Str::upper($stockOpname->outlet->name) }}</h3>
                                </div>
                            @endif


                            <div class="col-md-3">
                                <span>Tanggal </span>
                                <h3>{{ date('d F Y H:i A', strtotime($stockOpname->so_date)) }}</h3>
                            </div>

                            <div class="col-md-3">
                                <span>Status </span>
                                <h3>
                                    @if ($stockOpname->status == 'selesai')
                                        <span class="badge badge-sm bg-green">Selesai</span>
                                    @else
                                        <span class="badge badge-sm bg-yellow">Belum Selesai</span>
                                    @endif
                                </h3>
                            </div>

                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Nama Produk</th>
                                        <th>Qty</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stockOpnameDetail as $sod)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $sod->product->name }}</td>
                                            <td>{{ $sod->qty_so }}</td>
                                        </tr>
                                    @endforeach
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
@endpush
