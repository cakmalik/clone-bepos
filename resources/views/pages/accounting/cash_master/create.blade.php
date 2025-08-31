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

            @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <form action="/accounting/cash_master" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kode BK</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('code') is-invalid @enderror" name="code">
                                        @error('code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Item BK</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('name') is-invalid @enderror" name="name">
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Akun Debit</label>
                                        <select name="debit_account_id"
                                            class="form-control  @error('debit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Debit &mdash;
                                            </option>
                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('debit_account_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Akun Kredit</label>
                                        <select name="credit_account_id"
                                            class="form-control @error('credit_account_id') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                Kredit &mdash;
                                            </option>
                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('credit_account_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">BKK/BKM</label>
                                        <select name="cash_type" class="form-control"
                                            @error('cash_type') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih
                                                BKK/BKM &mdash;
                                            </option>
                                            <option>KAS-MASUK</option>
                                            <option>KAS-KELUAR</option>
                                        </select>
                                        @error('cash_type')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" style="margin-left:90%"><i
                                    class="fa-solid fa-floppy-disk"></i> &nbsp;Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
