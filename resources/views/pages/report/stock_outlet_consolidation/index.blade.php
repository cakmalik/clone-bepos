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
                        Laporan Stock Outlet Consolidation
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
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        @include('pages.report.stock_outlet_consolidation.outlet')

                        <div class="row mb-4">
                            <div class="col-6 d-flex align-items-top">
                              
                                <input type="date" class="form-control me-2" id="recapDate"
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                <a href="#" class="btn btn-primary me-2" data-bs-toggle="modal"data-bs-target="#consolidation-inventory">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Pilih Outlet
                                </a>
                                <button onclick="printPageArea()" class="btn btn-danger me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                        <rect x="7" y="13" width="10" height="8" rx="2"></rect>
                                    </svg>
                                    CETAK
                                </button>
                            </div>
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
          var app_url = {!! json_encode(url('/')) !!};
    let inventoryId = @json($inventory_id);
    let recapDate = $('#recapDate').val();

    let urlNota   = app_url+'/report/inventory/stock_outlet/consolidation-data?inventory='+inventoryId+'&recap_date='+recapDate;

    
    $(function() {
        // $('#printframe').attr('src', 'about:blank');
        // $('#loadingProgress').html(proses(1));
        $('#printframe').attr('src', urlNota);

        $('#inventoryTable').DataTable({
            destroy: true,
            paging: false,
        });
    });

    $('#recapDate').change(function() {
        refreshData();
    });

    function refreshData(){
        inventoryId = [];
        recapDate = $('#recapDate').val();


        $('.form-check-input:checked').each(function(){
            inventoryId.push($(this).val());
        });

        $('#consolidation-inventory').modal('hide');
        urlNota   = app_url+'/report/inventory/stock_outlet/consolidation-data?inventory='+inventoryId+'&recap_date='+recapDate;

        $('#printframe').attr('src', 'about:blank');
        $('#loadingProgress').html(proses(1));
        $('#printframe').attr('src', urlNota);
    }


    $('#refreshReport').click(function() {
        refreshData();
    });

    $('body').on('click', '#selectAll', function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    }) ;



    function printPageArea(){
        frame = document.getElementById("printframe");
        framedoc = frame.contentWindow;
        framedoc.focus();
        framedoc.print();
    }

    </script>
    @endpush