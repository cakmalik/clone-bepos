@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Laporan Laba Rugi
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
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Mulai</label>
                                <input id="input-start-date" type="date" class="form-control" name="start_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sampai</label>
                                <input id="input-end-date" type="date" class="form-control" name="end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Outlet</label>
                                <select name="outlet" id="outlet" class="form-control">
                                    @if (auth()->user()->role->role_name == 'SUPERADMIN')
                                        <option value="">-- Semua --</option>
                                    @endif
                                    @foreach ($outlet as $t)
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kasir</label>
                                <select name="user" id="user" class="form-control">
                                    <option value="">-- Semua --</option>
                                    @foreach ($users as $s)
                                        <option value="{{ $s->id }}">{{ $s->users_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 d-flex">
                                <div class="form-group">
                                    <button class="btn btn-primary" id="refreshReport">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                        </svg>
                                        REFRESH LAPORAN
                                    </button>
                                    <button onclick="printPageArea()" class="btn btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                            </path>
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                            <rect x="7" y="13" width="10" height="8"
                                                rx="2">
                                            </rect>
                                        </svg>
                                        CETAK
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
                                <iframe id="printframe" name="printframe"
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function loadReport() {
            $('#loadingProgress').html(proses(1));
            let url = '{{ url('/report/laba-rugi/print') }}'
            const params = new URLSearchParams()
            params.append('start_date', $('#input-start-date').val())
            params.append('end_date', $('#input-end-date').val())
            params.append('outlet', $('#outlet').val())
            params.append('user', $('#user').val())

            url += '?' + params.toString()
            $('#printframe').attr('src', url);
            
        }

        $('#outlet').select2()
        $('#user').select2()

        $('#refreshReport').click(function() {
            loadReport();
        })

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }

        $(document).ready(function() {
            loadReport()
        })
    </script>
@endpush
