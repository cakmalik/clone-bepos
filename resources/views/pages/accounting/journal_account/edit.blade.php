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
                        Edit Jurnal Akun
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        @if ($message = Session::get('error'))
                            <x-alert level="danger" message="{{ $message }}" />
                        @elseif($message = Session::get('success'))
                            <x-alert level="success" message="{{ $message }}" />
                        @endif
                        @foreach ($errors->all() as $error)
                            <x-alert level="danger" message="{{ $error }}" />
                        @endforeach
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Edit Jurnal Akun</h3>
                                </div>
                                <form action="{{ route('journal_account.update', $journalAccount->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="row">

                                                    <div class="mb-3">
                                                        <label class="form-label">Kode/ID Akun</label>
                                                        <input type="text" autocomplete="off"
                                                            class="form-control  @error('code') is-invalid @enderror"
                                                            name="code" value="{{ $journalAccount->code }}">
                                                        @error('code')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Akun</label>
                                                            <input type="text" autocomplete="off"
                                                                class="form-control  @error('name') is-invalid @enderror"
                                                                name="name" value="{{ $journalAccount->name }}">
                                                            @error('name')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Jenis Akun</label>
                                                            <select name="journal_account_type_id"
                                                                class="form-control @error('journal_account_type_id') is-invalid @enderror">
                                                                <option selected value="0" disabled> &mdash; Pilih Tipe
                                                                    &mdash;
                                                                </option>
                                                                @if ($type->count() > 0)
                                                                    @foreach ($type as $journalAccountType)
                                                                        <option value="{{ $journalAccountType->id }}"
                                                                            {{ $journalAccountType->id == $journalAccount->journal_account_type_id ? 'selected' : '' }}>
                                                                            {{ $journalAccountType->name }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @error('journal_account_type_id')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <div class="d-flex">
                                            <button type="reset" class="btn btn-link">Reset</button>
                                            <button type="submit" class="btn btn-success ms-auto"><i
                                                    class="fa-solid fa-floppy-disk"></i> &nbsp; Update
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
@endpush
