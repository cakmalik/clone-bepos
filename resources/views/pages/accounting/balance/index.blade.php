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
                        Neraca
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
                        <h3 class="card-title">Neraca</h3>
                    </div>
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <label class="form-label">Periode</label>
                                <select id="input-period" class="form-select" name="period">
                                    <option value="-">Periode Berjalan</option>
                                    @foreach ($journal_closings as $journal_closing)
                                        <option value="{{ $journal_closing->id }}">{{ $journal_closing->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input id="input-end-date" type="date" class="form-select" name="end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <label class="form-label">Tampilkan Akun di Laba Rugi?</label>
                                <select id="input-profil-loss" class="form-select" name="profit_loss">
                                    <option value="">Tidak</option>
                                    <option value="1">Ya</option>
                                </select>
                            </div>
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
                                <div id="balance-document"></div>
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
                url: '{{ url('/accounting/balance/print') }}',
                data: {
                    'period': $('#input-period').val(),
                    'profit_loss': $('#input-profil-loss').val(),
                    'end_date': $('#input-end-date').val(),
                },
                beforeSend: function() {
                    $('#loadingProgress').show();
                },
                success: function(data) {
                    $('#balance-document').html(data);
                },
                error: function(data) {
                    $('#balance-document').html(data);
                },
                complete: function(data) {
                    $('#loadingProgress').hide();
                }
            })
        }

        $('#input-period, #input-end-date, #input-profil-loss').on('change', function() {
            loadLedger();
        })

        $(function() {
            loadLedger();
        })
    </script>
@endpush
