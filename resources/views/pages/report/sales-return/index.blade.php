@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title text-uppercase">
                        Laporan Sales Return
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">Sales Return</h3>
                </div>
                <div class="card-body border-bottom py-3">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start">Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end">Sampai</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="outlet">Outlet</label>
                                <select name="outlet" id="outlet" class="form-select">
                                    <option value="-">SEMUA OUTLET</option>
                                    @foreach($outlet as  $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nota_type">Retur Penjualan Menurut</label>
                                <select name="nota_type" id="nota_type" class="form-select">
                                    <option value="SUMMARY" selected>Summary</option>
                                    <option value="DETAIL">DETAIL</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mt-auto">
                            <div class="form-group ">
                                <button class="btn btn-primary" id="refreshReport"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                 </svg> REFRESH LAPORAN</button>
                                <button onclick="printPageArea()" class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                    <rect x="7" y="13" width="10" height="8" rx="2"></rect>
                                 </svg>CETAK</button>
                                
                            </div>
                        </div>
                    </div> 
                    <div class="row mb-4">
                       
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


@endsection
@push('scripts')
<script>

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var app_url = {!! json_encode(url('/')) !!};

    let nota_type = $('#nota_type').val();
    let start_date  = $('#start_date').val();
    let end_date    = $('#end_date').val();


    let urlNota   = app_url+'/report/sales/return/print?nota_type='+nota_type+'&start_date='+start_date+'&end_date='+end_date;

    console.log(urlNota);

    $(function() {
        $('#printframe').attr('src', 'about:blank');
        $('#loadingProgress').html(proses(1));
        $('#printframe').attr('src', urlNota);
        
    });


    $('#refreshReport').click(function() {

        start_date  = $('#start_date').val();
        end_date    = $('#end_date').val();
        let outlett  = $('#outlet').val();
        nota_type   = $('#nota_type').val();
    
        let urlNota   = app_url+'/report/sales/return/print?nota_type='+nota_type+'&start_date='+start_date+'&end_date='+end_date+'&outlet='+outlett;
        $('#loadingProgress').html(proses(1));

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

