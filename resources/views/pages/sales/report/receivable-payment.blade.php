@include('layouts.header')
<div class="wrapper">
    @include('layouts.sidebar')
    <div class="page-wrapper">
        <div class="page-body">
            <div class="container-xl">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start" class="mb-2">Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end" class="mb-2">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="origin" class="mb-2">Customer</label>
                                    <input name="customer" id="customer" class="form-control" placeholder="-- By Customer --" readonly>
                                    <input type="hidden" id="customerId" name="customer_id">
                                    
                                </div>
                            </div>
                            <div class="col-md-3 mt-auto">
                                <div class="form-group">
                                    <button class="btn btn-primary" id="refreshReport"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                     </svg> REFRESH</button>
                                    <button onclick="printPageArea()" class="btn btn-danger"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                        <rect x="7" y="13" width="10" height="8" rx="2"></rect>
                                     </svg>CETAK</button>
                                    
                                </div>
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
        @include('layouts.footer')
        @include('sweetalert::alert')
    </div>
</div>

<div class="modal modal-blur fade" id="customerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover w-100" id="customerTable">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    var app_url = {!! json_encode(url('/')) !!};

    let start_date  = $('#start_date').val();
    let end_date    = $('#end_date').val();


    let urlNota   = app_url+'/report/receivable-payment-data?start_date='+start_date+'&end_date='+end_date;

    $(function() {
        $('#printframe').attr('src', 'about:blank');
        $('#loadingProgress').html(proses(1));
        $('#printframe').attr('src', urlNota);
    });


    $('#refreshReport').click(function() {

        start_date  = $('#start_date').val();
        end_date    = $('#end_date').val();
    
        let urlNota   = app_url+'/report/receivable-payment-data?start_date='+start_date+'&end_date='+end_date;
        $('#loadingProgress').html(proses(1));

        $('#printframe').attr('src', urlNota);

    });

    function printPageArea(){
        frame = document.getElementById("printframe");
        framedoc = frame.contentWindow;
        framedoc.focus();
        framedoc.print();
    }

    $('#customer').focus(function() {

        $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            destroy: true,
            ajax: {
                url: "{{ url('/customers') }}",
            },
            columns: [
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action'},
            ]
        });


        $('#customerModal').modal('show');
    })

    $('body').on('click', '#customerSelect', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        $('#customer').val(name);
        $('#customerId').val(id);

        $('#customerModal').modal('hide');

    });

</script>
