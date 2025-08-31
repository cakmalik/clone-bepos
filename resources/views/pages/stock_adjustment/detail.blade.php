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
                        Detail Adjustment
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
                        <h3 class="card-title"> Detail Adjustment</h3>
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-md-3">
                                <span>Kode Adjustment </span>
                                <h3>{{ $adjustment->code }}</h3>
                            </div>
                            <div class="col-md-3">
                                <span>Kode Opname </span>
                                <h3>{{ $adjustment->ref_code }}</h3>
                            </div>
                            @if ($adjustment->inventory_id)
                                <div class="col-md-3">
                                    <span>Nama Gudang </span>
                                    <h3>{{ Str::upper($adjustment->inventory->name) }}</h3>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <span>Nama Outlet </span>
                                    <h3>{{ Str::upper($adjustment->outlet->name) }}</h3>
                                </div>
                            @endif


                            <div class="col-md-3">
                                <span>Status </span>
                                <h3>
                                    <span class="badge badge-sm bg-green">{{ $adjustment->status }}</span>
                                </h3>
                            </div>

                            <div class="col-md-3">
                                <span>Tanggal </span>
                                <h3>{{ date('d F Y H:i A', strtotime($adjustment->so_date)) }}</h3>
                            </div>

                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Nama Produk</th>
                                        <th>Qty System</th>
                                        <th>Qty</th>
                                        <th>Selisih</th>
                                        <th>Qty Adjustment</th>
                                        <th>Qty Setelah Adjustment</th>
                                        <th>Nominal</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($adjustment_detail as $add)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $add->product->name }}</td>
                                            <td>{{ $add->qty_system }}</td>
                                            <td>{{ $add->qty_so }}</td>
                                            <td>{{ $add->qty_selisih }}</td>
                                            <td>{{ $add->qty_adjustment }}</td>
                                            <td>{{ $add->qty_after_adjustment }}</td>
                                            <td>Rp {{ number_format($add->adjustment_nominal_value, '0', ',', '.') }}</td>
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
