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
                        Buku Besar
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif
            @foreach ($errors->all() as $error)
                <x-alert level="danger" message="{{ $error }}" />
            @endforeach --}}

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Buku Besar</h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Periode</label>
                                <select id="input-period" class="form-select" name="period">
                                    <option value="-">Periode Berjalan</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Akun</label>
                                <select id="input-journal-account-id" class="form-select" name="journal_account_id">
                                    <option value="">Semua Akun</option>
                                    @foreach ($journalAccounts as $journalAccount)
                                        <option value="{{ $journalAccount->id }}">{{ $journalAccount->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Mulai Tanggal</label>
                                <input id="input-start-date" type="date" class="form-select" name="start_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Sampai Tanggal</label>
                                <input id="input-end-date" type="date" class="form-select" name="end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div id="loadingProgress">
                        <div class="progress progress-sm">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>

                    <div class="card-body border-top">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="button" class="btn btn-warning" onclick="window.print()">
                                    Cetak
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div id="ledger-document">
                                </div>
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
        function loadLedger() {
            $.ajax({
                type: 'GET',
                url: '{{ url('/accounting/ledger') }}',
                data: {
                    'period': $('#input-period').val(),
                    'journal_account_id': $('#input-journal-account-id').val(),
                    'start_date': $('#input-start-date').val(),
                    'end_date': $('#input-end-date').val(),
                },
                beforeSend: function() {
                    $('#loadingProgress').show();
                },
                success: function(data) {
                    $('#ledger-document').html(data);
                },
                error: function(data) {
                    $('#ledger-document').html(data);
                },
                complete: function(data) {
                    $('#loadingProgress').hide();
                }
            })
        }

        $('#input-period, #input-journal-account-id, #input-start-date, #input-end-date').on('change', function() {
            loadLedger();
        })

        $(function() {
            loadLedger();
        })
    </script>
@endpush
