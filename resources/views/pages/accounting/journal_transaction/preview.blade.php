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
                    <h2 class="page-title">
                        Journal Transaction Preview
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Journal Transaction Preview</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <button class="btn btn-success" onclick="printPageArea()"><svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-credit-card" width="24"
                                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="3" y="5" width="18" height="14"
                                            rx="3"></rect>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                        <line x1="7" y1="15" x2="7.01" y2="15"></line>
                                        <line x1="11" y1="15" x2="13" y2="15"></line>
                                    </svg>Cetak</button>
                            </div>
                            {{-- <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Mulai</label>
                                    <input type="date" class="form-control" name="start_date"
                                        id="journal_transaction_start_date" value="{{ $start_date }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">Sampai</label>
                                    <input type="date" class="form-control" name="end_date"
                                        id="journal_transaction_end_date" value="{{ $end_date }}" readonly>
                                </div>
                            </div> --}}

                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
                                <iframe id="printframe" name="printframe" style="width: 100%; height: 1500px; border:none;"></iframe>
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
    let app_url = {!! json_encode(url('/')) !!};
    let start_date = {!! json_encode($start_date) !!};
    let end_date = {!! json_encode($end_date) !!};

    let urlNota = app_url + '/accounting/journal-transaction-data?start_date=' + start_date + '&end_date=' + end_date;

    $(function() {
        $('#printframe').attr('src', urlNota);
    });


    function printPageArea(){
        frame = document.getElementById("printframe");
        framedoc = frame.contentWindow;
        framedoc.focus();
        framedoc.print();
    }
</script>
@endpush