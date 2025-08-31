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
                        Set Akun Baru
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
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
                        <h3 class="card-title">Set Akun Baru</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <form action="/accounting/journal_stock_account" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Kategori</label>
                                        <select name="category_id"
                                            class="form-control  @error('category_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Kategori &mdash;
                                            </option>
                                            @foreach ($category as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Debit Penjualan</label>
                                        <select name="sales_debit_account_id"
                                            class="form-control  @error('sales_debit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Debit &mdash;
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
                                        <label class="form-label">Kredit Penjualan</label>
                                        <select name="sales_credit_account_id"
                                            class="form-control  @error('sales_credit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Kredit &mdash;
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
                                        <label class="form-label">Debit Pembelian</label>
                                        <select name="purchase_debit_account_id"
                                            class="form-control  @error('purchase_debit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Debit &mdash;
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
                                        <label class="form-label">Kredit Pembelian</label>
                                        <select name="purchase_credit_account_id"
                                            class="form-control  @error('purchase_credit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Kredit &mdash;
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
                                        <label class="form-label">Debit INV.PO</label>
                                        <select name="inv_debit_account_id"
                                            class="form-control  @error('inv_debit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Debit &mdash;
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
                                        <label class="form-label">Kredit INV.PO</label>
                                        <select name="inv_credit_account_id"
                                            class="form-control  @error('inv_credit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Kredit &mdash;
                                            </option>

                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group text-end">
                                <button type="submit" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2">
                                        </path>
                                        <circle cx="12" cy="14" r="2"></circle>
                                        <polyline points="14 4 14 8 8 8 8 4"></polyline>
                                    </svg>
                                    Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
