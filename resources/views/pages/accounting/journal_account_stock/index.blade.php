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
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="btn-list justify-content-end">
                <a href="/accounting/journal_stock_account/create" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Buat Akun Barang
                </a>
            </div>
            <br>
            @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif
            @foreach ($errors->all() as $error)
                <x-alert level="danger" message="{{ $error }}" />
            @endforeach

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">

                            <div class="table-responsive">
                                <table
                                    class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Penjualan</th>
                                            <th>Pembelian</th>
                                            <th>Inv.PO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($journal_category_product as $jcp)
                                            <tr>
                                                <td>
                                                    <a href="/accounting/journal_stock_account/{{ $jcp->id }}/edit">
                                                        {{ $jcp->category->name }}</a>
                                                </td>
                                                <td>
                                                    <strong>Debit Penjualan:<br></strong>
                                                    {{ $jcp->journal_settings_trans->debit_account->name }}
                                                    <br>
                                                    <br>
                                                    <strong>Kredit Penjualan:<br></strong>
                                                    {{ $jcp->journal_settings_trans->credit_account->name }}
                                                </td>

                                                <td>
                                                    <strong>Debit Pembelian:<br></strong>
                                                    {{ $jcp->journal_settings_buy->debit_account->name }}
                                                    <br>
                                                    <br>
                                                    <strong>Kredit Pembelian:<br></strong>
                                                    {{ $jcp->journal_settings_buy->credit_account->name }}
                                                </td>

                                                <td>
                                                    <strong>Debit Inv.PO:<br></strong>
                                                    {{ $jcp->journal_settings_invoice->debit_account->name }}
                                                    <br>
                                                    <br>
                                                    <strong>Kredit Inv.PO:<br></strong>
                                                    {{ $jcp->journal_settings_invoice->credit_account->name }}
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
    </div>
@endsection
