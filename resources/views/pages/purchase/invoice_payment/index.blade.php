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
                        Pembayaran Faktur
                    </h2>
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
                                        <th style="width: 10%">Kode Supplier</th>
                                        <th>Supplier</th>
                                        <th style="width: 15%">Tanggal Invoice</th>
                                        <th style="width: 15%">Hutang</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase as $i)
                                        <tr>
                                            <td>{{ $i->code }}</td>
                                            <td>{{ $i->supplier }}</td>
                                            <td>{{ dateStandar($i->invoice_date) }}</td>
                                            <td>{{ rupiah($i->debt - $i->nominal_paid) }}</td>
                                            <td>
                                                <a href="/invoice_payment/{{ $i->supplier_id }}" class="btn btn-primary">
                                                    Bayar
                                                </a>
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
