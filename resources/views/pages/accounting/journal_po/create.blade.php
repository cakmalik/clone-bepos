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
                        <h3 class="card-title">
                            {{ $invoicePayment->code . ' ' . dateWithTime($invoicePayment->payment_date) }} <br>
                            {{ $invoicePayment->purchase_invoice_new->purchase->supplier->code . ' - ' . $invoicePayment->purchase_invoice_new->purchase->supplier->name }}
                            <br>
                            {{ $invoicePayment->payment_type . ' - ' . rupiah($invoicePayment->nominal_payment) }}
                        </h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <form action="/accounting/journal_po" method="post">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="journal_account_debit" value="{{ $debit->id }}">
                                <input type="hidden" name="invoice_id" value="{{ $invoicePayment->id }}">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kredit</label>
                                        <select name="journal_account_kredit"
                                            class="form-control @error('journal_account_kredit') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih Kredit
                                                &mdash;
                                            </option>

                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach

                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nominal</label>
                                        <input type="text" class="form-control @error('nominal') is-invalid @enderror"
                                            autocomplete="off" name="nominal"
                                            value="{{ 'Rp. ' . number_format($invoicePayment->nominal_payment, 0, ',', '.') }}"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('description') is-invalid @enderror"
                                            name="description">
                                    </div>
                                </div>


                            </div>
                            <div class="form-group text-end">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
