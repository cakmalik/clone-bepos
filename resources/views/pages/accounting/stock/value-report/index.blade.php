@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
@endpush

@section('page')
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Laporan Nilai Stok</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <label for="nota_option" class="mb-2">Laporan Nilai Stok</label>
                        <div class="row mb-4">
                            <div class="col-auto">
                                <div class="form-group">
                                    <button class="btn btn-success" onclick="printPageArea()">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-credit-card" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="3" y="5" width="18" height="14"
                                                  rx="3">
                                            </rect>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                            <line x1="7" y1="15" x2="7.01" y2="15"></line>
                                            <line x1="11" y1="15" x2="13" y2="15"></line>
                                        </svg>
                                        Cetak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <iframe id="printframe" name="printframe"
                                        style="width: 100%; height: 1500px; border:none;"
                                        src="{{ url('/accounting/stock/value-report/print') }}"></iframe>
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
        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
