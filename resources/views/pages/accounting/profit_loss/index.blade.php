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

            </div>

            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif --}}

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="periode">Periode</label>
                                    <select name="periode" id="periode" class="form-select">
                                        <option value="-" selected>-- Periode Berjalan --</option>
                                        @foreach ($periode as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">Mulai Tanggal</label>
                                    <input type="date" name="start_date" id="startDate" class="form-control"
                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">Sampai Tanggal</label>
                                    <input type="date" name="end_date" id="endDate" class="form-control"
                                        value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="btn-list">
                                <a href="javascript:void(0)" class="btn btn-primary" id="refreshReport">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                    </svg>
                                    Refresh
                                </a>
                                <button onclick="printPageArea()" class="btn btn-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                        </path>
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                        <rect x="7" y="13" width="10" height="8" rx="2">
                                        </rect>
                                    </svg>CETAK</button>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
                                <iframe id="printframe" name="printframe" title="Laba Rugi"
                                    style="width: 100%; height: 1500px; border:none;"></iframe>
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
        let appUrl = {!! json_encode(url('/')) !!};
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        let periode = $('#periode').val();

        let urlNota = appUrl + '/accounting/profit-loss-data?start_date=' + startDate +
            '&end_date=' + endDate + '&periode=' + periode;



        $(function() {
            $('#printframe').attr('src', 'about:blank');
            $('#loadingProgress').html(proses(1));
            $('#printframe').attr('src', urlNota);
        });


        $('#refreshReport').click(function() {
            startDate = $('#startDate').val();
            endDate = $('#endDate').val();
            periode = $('#periode').val();

            urlNota = appUrl + '/accounting/profit-loss-data?start_date=' + startDate +
                '&end_date=' + endDate + '&periode=' + periode;

            $('#printframe').attr('src', 'about:blank');
            $('#loadingProgress').html(proses(1));
            $('#printframe').attr('src', urlNota);
        });

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
