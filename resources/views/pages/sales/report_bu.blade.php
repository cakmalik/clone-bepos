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
                    {{-- <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="branch_office">Cabang</label>
                                    <select name="branch_office" id="branch_office" class="form-select">
                                        <option value="-">SEMUA CABANG</option>
                                        @foreach ($office as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="report_type">Penjualan Menurut</label>
                                    <select name="report_type" id="report_type" class="form-select">
                                        <option value="DETAIL" selected>DETAIL</option>
                                        <option value="SUMMARY">SUMMARY</option>
                                        <option value="CUSTOMER">SUMMARY PER CUSTOMER</option>
                                        <option value="ITEM">SUMMARY PER ITEM</option>
                                        <option value="ITEM-WITH-RETUR">SUMMARY PER ITEM DGN RETUR</option>
                                        <option value="ITEM-CATEGORY">SUMMARY PER KATEGORI</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="area">Area</label>
                                    <select name="area" id="area" class="form-select">
                                        <option value="">-- By Area --</option>
                                        @foreach ($area as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sub_area">Sub Area</label>
                                    <select name="subarea" id="subarea" class="form-select">
                                        <option value="">-- By Sub Area --</option>
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customer" id="customer" class="form-select">
                                        <option value="">-- By Pelanggan --</option>
                                        @foreach ($customer as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sales">Sales</label>
                                    <select name="sales" id="sales" class="form-select">
                                        <option value="">-- By Sales --</option>
                                        @foreach ($sales as $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
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
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
                                <iframe id="printframe" name="printframe" style="width: 100%; height: 1500px; border:none;"></iframe>
                            </div>
                        </div>                                   
                    </div> --}}
                </div>
            </div>
        </div>
        @include('layouts.footer')
        @include('sweetalert::alert')
    </div>
</div>
{{-- 
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var app_url = {!! json_encode(url('/')) !!};

    let report_type = $('#report_type').val();
    let start_date = $('#start_date').val();
    let end_date = $('#end_date').val();

    let urlNota = app_url + '/report/sales-data?report_type=' + report_type + '&start_date=+' + start_date +
        '&end_date=' + end_date;

    $(function() {
        $('#printframe').attr('src', 'about:blank');
        $('#loadingProgress').html(proses(1));
        $('#printframe').attr('src', urlNota);
    });


    $('#refreshReport').click(function() {
        report_type = $('#report_type').val();
        start_date = $('#start_date').val();
        end_date = $('#end_date').val();

        let office = $('#branch_office').val();
        let customer = $('#customer').val();
        let sales = $('#sales').val();
        let area = $('#area').val();
        let subarea = $('#subarea').val();

        let urlNota = app_url + '/report/sales-data?report_type=' + report_type +
            '&start_date=' + start_date + '&end_date=' + end_date + '&branch_office=' + office +
            '&customer=' + customer + '&sales=' + sales + '&area=' + area + '&subarea=' + subarea;
        $('#loadingProgress').html(proses(1));

        $('#printframe').attr('src', urlNota);

    });

    function printPageArea() {
        frame = document.getElementById("printframe");
        framedoc = frame.contentWindow;
        framedoc.focus();
        framedoc.print();
    }


    $('#area').on('change', function() {
        let area_id = $('#area').val();

        $.ajax({
            type: 'GET',
            url: "/api/subarea?area_id=" + area_id,
            dataType: 'json',
            success: function(data) {
                $('#subarea').empty();
                $('#subarea').append("<option value=''>-- Pilih Subarea --</option>")
                if (data.data.length > 0) {
                    $.each(data.data, function(key, value) {
                        $('#subarea').append("<option value=" + value.id + ">" + value
                            .name + "</option>")
                    });

                } else {
                    $('#subarea').append("<option value=''>-- Belum Ada Subarea --</option>")
                }

            }
        });
    });

    $(document).ready(function() {
        $('.form-select').select2();
    });
</script> --}}
