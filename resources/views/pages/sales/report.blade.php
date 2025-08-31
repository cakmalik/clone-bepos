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
                    Laporan Penjualan
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="card py-3">
                <div class="card-body border-bottom py-3">
                    <div class="row mb-4">
                        <div class="col-md-2  mt-auto">
                            <div class="form-group mt-auto" id="outlet_filter">
                                <label for="outlet_id" class="pb-1">Outlet</label>
                                <select name="outlet_id" id="outlet_id" class="form-select">\
                                    @if(Auth::user()->role->role_name == 'SUPERADMIN')
                                        <option value="">SEMUA</option>
                                    @endif
                                    @foreach ($outlet as $value)
                                        <option value="{{ $value->id }}"
                                            {{ $value->id == old('outlet_id', $selectedOutlet) ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>                                

                            </div>
                        </div>
                        <div class="col-md-2  mt-auto">
                            <div class="form-group mt-auto">
                                <label for="report_type" class="pb-1">Penjualan Menurut</label>
                                <select name="category" id="report_type" class="form-select">
                                    <option value="DETAIL" selected>DETAIL</option>
                                    <option value="SIMPLE">NOTA</option>
                                    <option value="SUMMARY">SUMMARY</option>
                                    <option value="USER">KASIR</option>
                                    <option value="CUSTOMER">CUSTOMER</option>
                                    <option value="CUSTOMER_REGION">WILAYAH CUSTOMER</option>
                                    <option value="CATEGORY">KATEGORI PRODUK</option>
                                    <option value="SUPPLIER">SUPPLIER</option>
                                    <option value="PRODUCT">PRODUK</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mt-auto" id="user_filter">
                            <div class="form-group mt-auto">
                                <label for="user_id" class="pb-1">Kasir</label>
                                <select name="user_id" id="user_id" class="form-select">
                                    <option value="">SEMUA</option>
                                    <!-- AJAX --->

                                    @if(Auth::user()->role->role_name != 'SUPERADMIN')
                                        @foreach ($users as $i)
                                            <option value="{{ $i->user_id }}">{{ $i->users_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mt-auto" id="cust_filter">
                            <div class="form-group mt-auto">
                                <label for="customer_id" class="pb-1">Customer</label>
                                <select name="customer_id" id="customer_id" class="form-select">
                                    <option value="">Pilih Customer</option>
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2  mt-auto" id="product_category_filter">
                            <div class="form-group mt-auto">
                                <label for="product_category_id" class="pb-1">Kategori Produk</label>
                                <select name="product_category_id" id="product_category_id" class="form-select">
                                    <option value="" selected>SEMUA</option>
                                    @foreach ($category as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2  mt-auto" id="supplier_filter">
                            <div class="form-group mt-auto">
                                <label for="supplier_id" class="pb-1">Supplier Produk</label>
                                <select name="supplier_id" id="supplier_id" class="form-select">
                                    <option value="" selected>SEMUA</option>
                                    @foreach ($suppliers as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2  mt-auto" id="product_filter">
                            <div class="form-group mt-auto">
                                <label for="product_id" class="pb-1">Produk</label>
                                <select name="product_id" id="product_id" class="form-select">
                                    <option value="" selected>SEMUA</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                
                            </div>
                        </div>
                        <div class="col-md-2  mt-auto">
                            <div class="form-group mt-auto">
                                <label for="payment_method_id" class="pb-1">Metode Pembayaran</label>
                                <select name="category" id="payment_method_id" class="form-select">
                                    <option value="" selected>SEMUA</option>
                                    @foreach ($payments as $i)
                                    <option value="{{ $i->id }}">{{ $i->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                    </div>
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="start" class="pb-1">Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="end" class="pb-1">Sampai</label>
                                <input type="date" class="form-control" name="end_date" id="end_date"
                                    value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button class="btn btn-primary" id="refreshReport"><svg
                                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                    </svg> REFRESH LAPORAN</button>
                                <button onclick="printPageArea()" class="btn btn-danger"><svg
                                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path
                                            d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2">
                                        </path>
                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                        <rect x="7" y="13" width="10" height="8" rx="2">
                                        </rect>
                                    </svg>CETAK</button>
                                <button id="resetButton" class="btn btn-secondary">
                                    <img style="margin-right: 5px;" width="16" height="16"
                                        src="https://img.icons8.com/ios-filled/50/FFFFFF/recurring-appointment.png"
                                        alt="recurring-appointment" />
                                    <span style="margin-left: 5px;">RESET</span>
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
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('jquery.mask.min.js') }}"></script>
<script>
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var app_url = {!! json_encode(url('/')) !!};
        let user_id = $('#user_id').val();
        let report_type = $('#report_type').val();
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        let payment_method_id = $('#payment_method_id').val();
        let product_category_id = $('#product_category_id').val();
        let supplier_id = $('#supplier_id').val();
        let product_id = $('#product_id').val();
        let outlet_id = $('#outlet_id').val();

        let urlNota = app_url + '/report/sales-data?report_type=' + report_type +
                '&start_date=' + start_date + '&end_date=' + end_date + '&outlet_id=' + outlet_id
                + '&user_id=' + user_id + '&payment_method_id=' + payment_method_id + '&customer_id=' + customer_id
                + '&product_category_id='+ product_category_id + '&supplier_id='+supplier_id + '&product_id='+product_id;

        $(function() {
            $('#printframe').attr('src', 'about:blank');
            $('#loadingProgress').html(proses(1));
            $('#printframe').attr('src', urlNota);
        });

        $('#refreshReport').click(function() {
            report_type = $('#report_type').val();
            start_date = $('#start_date').val();
            end_date = $('#end_date').val();
            user_id = $('#user_id').val();
            payment_method_id = $('#payment_method_id').val();
            customer_id = $('#customer_id').val();
            product_category_id = $('#product_category_id').val();
            supplier_id = $('#supplier_id').val();
            outlet_id = $('#outlet_id').val();
            product_id = $('#product_id').val();
            
            let urlNota = app_url + '/report/sales-data?report_type=' + report_type +
                '&start_date=' + start_date + '&end_date=' + end_date + '&outlet_id=' + outlet_id
                + '&user_id=' + user_id + '&payment_method_id=' + payment_method_id + '&customer_id=' + customer_id
                + '&product_category_id='+ product_category_id + '&supplier_id='+supplier_id + '&product_id='+product_id;


            $('#loadingProgress').html(proses(1));
            $('#printframe').attr('src', urlNota);
        });

        function exportExcel() {
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.src = "{{ route('generate.excel') }}?user_id=" + $('#user_id').val() +
                "&report_type=" + $('#report_type').val() +
                "&start_date=" + $('#start_date').val() +
                "&end_date=" + $('#end_date').val() +
                "&payment_method_id=" + $('#payment_method_id').val() +
                "&customer_id=" + $('#customer_id').val() +
                "&_token={{ csrf_token() }}";
            iframe.onload = function() {
                document.body.removeChild(iframe);
            };
        }

        function printPageArea() {
            Swal.fire({
                title: 'Select Report Type',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'PDF',
                cancelButtonText: 'Excel',
            }).then((result) => {
                if (result.isConfirmed) {
                    frame = document.getElementById("printframe");
                    framedoc = frame.contentWindow;
                    framedoc.focus();
                    framedoc.print();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    exportExcel();
                }
            });
        }


        $(document).ready(function() {
            $('.form-select').select2();
        });


        function iframeLoaded() {
            $('#loadingProgress').html(proses(0));
        }


        function proses(action) {
            let htmlData = '';
            if (action == 1) {
                htmlData =
                    '<div class="progress progress-sm"><div class="progress-bar progress-bar-indeterminate"></div></div>';
            }
            return htmlData;
        }

        $body = $("body");

        $(document).on({
            ajaxStart: function() {
                $body.addClass("loading");
            },
            ajaxStop: function() {
                $body.removeClass("loading");
            }
        });

        $(".btn[type='submit']").on('click', function(e) {

            $body = $("body");
            $body.addClass("loading");
        });
</script>
<script>
    $(document).ready(function() {
            var reportType = $('#report_type').val();

            if (reportType === 'USER') {
                $('#user_filter').show();
            } else {
                $('#user_filter').hide();
            }

            if (reportType === 'CUSTOMER') {
                $('#cust_filter').show();
            } else {
                $('#cust_filter').hide();
            }

            if (reportType === 'CATEGORY') {
                $('#product_category_filter').show();
            } else {
                $('#product_category_filter').hide();
            }

            if (reportType === 'SUPPLIER') {
                $('#supplier_filter').show();
            } else {
                $('#supplier_filter').hide();
            }

            if (reportType === 'PRODUCT') {
                $('#product_filter').show();
            } else {
                $('#product_filter').hide();
            }

            $('#report_type').change(function() {
                reportType = $(this).val();

                if (reportType === 'USER') {
                    $('#user_filter').show();
                } else {
                    $('#user_filter').hide();
                }

                if (reportType === 'CUSTOMER') {
                    $('#cust_filter').show();
                } else {
                    $('#cust_filter').hide();
                }

                if (reportType === 'CATEGORY') {
                    $('#product_category_filter').show();
                } else {
                    $('#product_category_filter').hide();
                }

                if (reportType === 'SUPPLIER') {
                    $('#supplier_filter').show();
                } else {
                    $('#supplier_filter').hide();
                }

                if (reportType === 'PRODUCT') {
                    $('#product_filter').show();
                } else {
                    $('#product_filter').hide();
                }
            });
        });
</script>
<script>

    $(document).ready(function() {

        $('#outlet_id').on('change', function() {
            let outletId = $(this).val(); 
            let userDropdown = $('#user_id');

            userDropdown.empty();
            userDropdown.append('<option value="">SEMUA</option>');

            if (outletId) {
                $.ajax({
                    url: '{{ route("cashier.byOutlet") }}',
                    type: 'GET',
                    data: { outlet_id: outletId },
                    success: function(data) {
                        data.forEach(function(cashier) {
                            userDropdown.append(
                                `<option value="${cashier.user_id}">${cashier.users_name}</option>`
                            );
                        });
                    },
                    error: function() {
                        alert('Gagal mengambil data kasir!');
                    }
                });
            }
        });
    });
</script>
<script>
    document.getElementById('resetButton').addEventListener('click', function(event) {
            window.location.reload();
    });
</script>
@endpush