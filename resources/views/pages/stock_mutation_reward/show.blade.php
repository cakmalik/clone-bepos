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
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="form-group">
                    <a href="/stock_mutation_reward/" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali
                    </a>
                </div>



                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div class="mt-2 mb-2">
                                        <h3>Outlet : {{ $outlet->outlet->name }}</h3>
                                        <h3>Tipe : {{ $type->type }}</h3>
                                    </div>

                                    <table
                                        class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nama Produk</th>
                                                <th>Tanggal</th>
                                                <th>Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reward as $r)
                                                <tr>
                                                    <td>{{ $r->product->name }}</td>
                                                    <td>{{ dateWithTime($r->date) }}</td>
                                                    <td>{{ $r->qty }}</td>
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
        </div>
    </div>
@endsection
