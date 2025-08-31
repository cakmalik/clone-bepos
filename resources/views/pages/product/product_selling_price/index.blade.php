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
                    <h2 class="page-title mb-0">
                        Harga Penjualan
                    </h2>
                </div>
                
                <div class="col-auto">
                    @include('pages.product.product_selling_price.create')
                </div>
            </div>
        </div>        
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Name</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataSellingPrice as $pc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pc->name }}</td>
                                            <td>
                                                @include('pages.product.product_selling_price.edit')
                                                @include('pages.product.product_selling_price.destroy')
                                            </td>
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
