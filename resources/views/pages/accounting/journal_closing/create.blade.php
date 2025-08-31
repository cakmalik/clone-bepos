@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-uppercase">
                        Journal
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
                        {{-- @if ($message = Session::get('error'))
                            <x-alert level="danger" message="{{ $message }}"/>
                        @elseif($message = Session::get('success'))
                            <x-alert level="success" message="{{ $message }}"/>
                        @endif
                        @foreach ($errors->all() as $error)
                            <x-alert level="danger" message="{{ $error }}" />
                        @endforeach --}}
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tambah Jurnal</h3>
                                </div>
                                <form id="create-journal-transaction-form"
                                      action="{{ route('journal_transaction.store') }}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tanggal</label>
                                                            <input type="date" autocomplete="off"
                                                                   class="form-control  @error('date') is-invalid @enderror"
                                                                   name="date" value="{{ old('date') }}">
                                                            @error('date')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Tipe Jurnal</label>
                                                            <select name="journal_type_id"
                                                                    class="form-control @error('journal_type_id') is-invalid @enderror">
                                                                <option selected value="0" disabled> &mdash; Pilih Tipe
                                                                    &mdash;
                                                                </option>
                                                                @foreach ($journalTypes as $journalType)
                                                                    <option
                                                                        value="{{ $journalType->id }}" {{ old('journal_type_id') == $journalType->id ? 'selected' : '' }}>{{ $journalType->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('journal_type_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Debit</label>
                                                            <select name="journal_debit_account_id"
                                                                    class="form-control @error('journal_debit_account_id') is-invalid @enderror">
                                                                <option selected value="0" disabled> &mdash; Pilih Debit
                                                                    &mdash;
                                                                </option>
                                                                @foreach ($journalAccounts as $journalAccount)
                                                                    <option
                                                                        value="{{ $journalAccount->id }}" {{ old('journal_debit_account_id') == $journalAccount->id ? 'selected' : '' }}>{{ $journalAccount->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('journal_debit_account_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Debit</label>
                                                            <select name="journal_credit_account_id"
                                                                    class="form-control @error('journal_credit_account_id') is-invalid @enderror">
                                                                <option selected value="0" disabled> &mdash; Pilih
                                                                    Kredit &mdash;
                                                                </option>
                                                                @foreach ($journalAccounts as $journalAccount)
                                                                    <option
                                                                        value="{{ $journalAccount->id }}" {{ old('journal_credit_account_id') == $journalAccount->id ? 'selected' : '' }}>{{ $journalAccount->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('journal_credit_account_id')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Outlet</label>
                                                            <select name="outlet_id"
                                                                    class="form-control @error('outlet_id') is-invalid @enderror">
                                                                @foreach ($outlets as $outlet)
                                                                    <option
                                                                        value="{{ $outlet->id }}" {{ old('outlet_id') ? (old('outlet_id') == $outlet->id ? 'selected' : '') : ($activeOutlet->id == $outlet->id ? 'selected' : '') }}>
                                                                        {{ $outlet->name }}{{ $activeOutlet->id == $outlet->id ? ' (Main Outlet)' : '' }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nominal</label>
                                                            <input type="text"
                                                                   class="form-control currency @error('nominal') is-invalid @enderror"
                                                                   data-currency-value="#nominal-value">
                                                            <input id="nominal-value" type="hidden" name="nominal"
                                                                   value="{{ old('nominal') }}">
                                                            @error('nominal')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea type="text"
                                                                      class="form-control @error('description') is-invalid @enderror"
                                                                      name="description">{{ old('description') }}</textarea>
                                                            @error('description')
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
                                            <button type="submit" class="btn btn-primary ms-auto"><i
                                                    class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
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
    <script>
        $(document).ready(function () {
            $('.currency').each(function () {
                const value_element = $($(this).data('currency-value'))
                const data_real = value_element.val() || 0
                $(this).val(generateCurrency(Number(data_real)))
                    .on('input', function () {
                        const number = parseCurrency(this.value)
                        const text = generateCurrency(number)
                        value_element.val(number)
                        $(this).val(text)
                    })
            })
        })
    </script>
@endpush
