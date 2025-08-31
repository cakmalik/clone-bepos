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
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        Faktur Pembelian
                    </h2>

                    <div>
                        <a href="{{ route('purchaseInvoice.create') }}" class="btn btn-primary btn-xl ms-auto">
                            <i class="fa-solid fa-plus"></i> &nbsp; Faktur
                        </a>
                        <button class="btn btn-danger" type="button" id="exportPdf">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                <path d="M17 18h2" />
                                <path d="M20 15h-3v6" />
                                <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                            </svg>
                            Export PDF
                        </button>
                        <button class="btn btn-success" type="button" id="exportExcel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M8 11h8v7h-8z" />
                                <path d="M8 15h8" />
                                <path d="M11 11v7" />
                            </svg>
                            Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="" selected>-- SEMUA --</option>
                                        <option value="0">BELUM LUNAS</option>
                                        <option value="1">LUNAS</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                id="dataTable">
                                <thead>
                                    <tr>
                                        <th>No.Tagihan</th>
                                        <th>Kode PO</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="10%">Total Tagihan</th>
                                        <th width="10%">Nominal Retur</th>
                                        <th width="10%">Dibayar</th>
                                        <th width="5%">Status</th>
                                        <th>Aksi</th>
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
    </div>

    <div class="modal modal-blur fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Tagihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailBody">
                    <!-- Content will be dynamically populated here via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {

            let appUrl = {!! json_encode(url('/')) !!};
            let status = $('#status').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                // jika diterapkan, saat mau retur - bug: tidak muncul produk
                // order: [
                //     [2, 'desc']
                // ],


                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },

                columns: [{
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'po_code',
                        name: 'po_code'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'nominal_total_inv',
                        name: 'nominal_total_inv'
                    },
                    {
                        data: 'nominal_returned_rp',
                        name: 'nominal_returned_rp'
                    },
                    {
                        data: 'nominal_paid_rp',
                        name: 'nominal_paid_rp'
                    },
                    {
                        data: 'is_done',
                        name: 'is_done'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    }

                ]
            });

            $('#start_date, #end_date, #status').change(function() {
                start_date = $('#start_date').val();
                end_date = $('#end_date').val();
                status = $('#status').val();

                table.draw();
            });

            $('#exportPdf').on('click', function() {
                let urlExport = appUrl + '/purchase_invoice/export?type=pdf&status=' + status +
                    '&start_date=' + start_date + '&end_date=' + end_date;

                window.open(urlExport, "_blank");
            });

            $('#exportExcel').on('click', function() {
                let urlExport = appUrl + '/purchase_invoice/export?type=excel&status=' + status +
                    '&start_date=' + start_date + '&end_date=' + end_date;

                window.open(urlExport, "_blank");
            });

            $(document).ready(function() {
                $(document).on('click', '#viewDetailButton', function() {
                    var invoiceId = $(this).data('id');

                    $.ajax({
                        url: '/purchase_invoice/' +
                            invoiceId,
                        type: 'GET',
                        success: function(response) {
                            $('#modalDetailBody').html(
                                response);
                        },
                        error: function() {
                            alert('Error fetching data.');
                        }
                    });
                });
            });

        });
    </script>
@endpush
