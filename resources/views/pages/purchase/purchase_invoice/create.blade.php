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
                        Faktur Pembelian
                    </h2>
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
                        <form action="{{ route('purchaseInvoice.store') }}" method="post" class="form-detect-unsaved">
                            @csrf

                            <input type="hidden" name="id" id="invoice_purchase_id">
                            <input type="hidden" name="code" value="{{ invoicePurchaseCode() }}">
                            <input type="hidden" name="time" id="timeInvoices">
                            <input type="hidden" name="nominal_returned" value="0">
                            <input type="hidden" name="nominal_paid" value="0">

                            <div class="row mb-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">No. Tagihan Supplier</label>
                                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                        name="invoice_number" autocomplete="off" placeholder="Masukkan Nomor Tagihan">
                                    @error('invoice_number')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 d-flex gap-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#po-invoice-create">
                                        <i class="fa-solid fa-magnifying-glass me-2"></i> Pesanan (PO)
                                    </button>

                                    @include('pages.purchase.purchase_invoice.purchase_order')

                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i> Selesai
                                    </button>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Tanggal</label>
                                        <input type="date" autocomplete="off"
                                            class="form-control  @error('invoice_date') is-invalid @enderror"
                                            name="invoice_date" value="<?= date('Y-m-d') ?>">
                                        @error('invoice_date')
                                            <div class=" alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Kode PO</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('po_code') is-invalid @enderror" name="po_code"
                                            id="invoice_purchase_code" value="{{ old('po_code') }}" readonly>
                                        @error('po_code')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Supplier</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('supplier') is-invalid @enderror" name="supplier"
                                            id="invoice_purchase_supplier" value="{{ old('supplier') }}" readonly>
                                        @error('supplier')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Nominal</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('nominal') is-invalid @enderror" name="nominal"
                                            id="invoice_purchase_nominal" value="{{ old('nominal') }}" readonly>
                                        @error('nominal')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label class="form-label">Nominal Diskon</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control @error('nominal') is-invalid @enderror"
                                            name="nominal_discount" id="invoice_purchase_discount" placeholder="0">
                                        @error('nominal_discount')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <input type="text" autocomplete="off"
                                    class="form-control @error('nominal') is-invalid @enderror" name="total_invoice"
                                    id="invoice_purchase_total" hidden>

                                <div class="row mt-3">
                                    <div class="col-md-10">
                                        <h2 class="text-end">Total Tagihan</h2>
                                    </div>
                                    <div class="col-md-2">
                                        <h2 id="invoice_purchase_total_display">Rp. 0</h2>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.setInterval(function() {
            $('#timeInvoices').val(moment().format('H:mm:ss'))
        }, 1000);

        $(document).ready(function() {

            $('body').on('change', '#purchase_po_id', function() {
                let id = $(this).attr('data-id');
                let code = $(this).attr('data-code');
                let supplier = $(this).attr('data-supplier');
                let subtotal = $(this).attr('data-subtotal');

                $('#invoice_purchase_id').val(id);
                $('#invoice_purchase_code').val(code);
                $('#invoice_purchase_supplier').val(supplier);
                $('#invoice_purchase_nominal').val(subtotal);
                $('#po-invoice-create').modal('hide');
                updateTotalTagihan();
            });

            $('#invoice_purchase_nominal, #invoice_purchase_discount').on('input', function() {
                updateTotalTagihan();
            });

            function updateTotalTagihan() {
                let nominal = parseFloat($('#invoice_purchase_nominal').val().replace(/[^0-9.-]+/g, "")) || 0;
                let nominal_discount = parseFloat($('#invoice_purchase_discount').val().replace(/[^0-9.-]+/g,
                    "")) || 0;
                let total_invoice = nominal - nominal_discount;

                $('#invoice_purchase_total').val(total_invoice);
                localStorage.setItem('total_invoice', total_invoice);
                $('#invoice_purchase_total_display').text(formatCurrency(total_invoice));
            }

            $(document).ready(function() {
                var storedTotalInvoice = localStorage.getItem('total_invoice');
                if (storedTotalInvoice) {
                    $('#invoice_purchase_total_display').text(formatCurrency(storedTotalInvoice));
                }

                localStorage.removeItem('total_invoice');
            });


            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }

        });
    </script>

    <script>
        document.querySelectorAll('.invoice-button').forEach(button => {
            button.addEventListener('click', function () {
                let id = this.getAttribute('data-id');
                let code = this.getAttribute('data-code');
                let supplier = this.getAttribute('data-supplier');
                let subtotal = this.getAttribute('data-subtotal');

                document.querySelectorAll('.invoice-button').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                    btn.querySelector('.button-text').textContent = 'Pilih';
                    btn.querySelector('.icon-check').style.display = 'none';
                });

                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                this.querySelector('.button-text').textContent = 'Dipilih';
                this.querySelector('.icon-check').style.display = 'inline-block';

                $('#purchase_po_id')
                    .val('')
                    .trigger('change')
                    .val(id)
                    .attr('data-id', id)
                    .attr('data-code', code)
                    .attr('data-supplier', supplier)
                    .attr('data-subtotal', subtotal)
                    .trigger('change');
            });
        })
    </script>
@endpush
