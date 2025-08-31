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
                    <h2 class="page-title text-uppercase">
                        Pembayaran PO/Hutang
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="btn-list justify-content-end">

            </div>

            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif --}}

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> Pembayaran PO/Hutang</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table
                                    class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Supplier</th>
                                            <th>Supplier</th>
                                            <th>Metode Pembayaran</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($purchaseInvices as $p)
                                            @foreach ($p->invoicePayments as $ip)
                                                @if ($ip->journal_number_id == null)
                                                    <tr>
                                                        <td>
                                                            <a
                                                                href="/accounting/journal_po/{{ $ip->id }}">{{ $p->purchase->supplier->code }}</a>
                                                        </td>
                                                        <td>{{ $p->purchase->supplier->name }}</td>
                                                        <td>{{ $ip->payment_type }}</td>
                                                        <td>{{ rupiah($ip->nominal_payment) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
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
@endsection
