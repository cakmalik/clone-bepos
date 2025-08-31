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
                        Pembayaran Faktur
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="card">
                    <div class="pt-3 pb-1 px-3">
                         <div class="row">
                            <div class="col-md-4">
                                <h5 class="text-muted"> Kode Pembayaran Tagihan </h5>
                                <h3 class="font-weight-bold text-primary">{{ invoicePaymentCode() }}</h3>
                            </div>

                            <div class="col-md-4">
                                <h5 class="text-muted"> Supplier </h5>
                                <h3 class="font-weight-bold text-dark">{{ $purchase->supplier }}</h3>
                            </div>

                            <div class="col-md-4">
                                <h5 class="text-muted"> Tanggal Pembayaran </h5>
                                <h3 class="font-weight-bold text-dark" id="timePayment"></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card py-3">
                    <div class="card-body py-3">

                       

                        <form action="/invoice_payment/store" method="post" class="form-detect-unsaved">
                            @csrf
                            <input type="hidden" name="id_invoice" id="id_invoice">
                            <input type="hidden" name="code" value="{{ invoicePaymentCode() }}">
                            <input type="hidden" name="payment_date" id="timeNowPayment">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Metode Pembayaran</label>
                                    <select name="payment_method_id"
                                        class="form-control @error('payment_method_id') is-invalid @enderror">
                                        <option selected disabled value=""> &mdash; Pilih Pembayaran &mdash;</option>
                                        @foreach($paymentMethods as $key => $value)
                                            <option value="{{ $value->id }}" {{ old('payment_method_id') == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_method_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Nominal</label>
                                    <div class="input-group">
                                        <button type="button" class="btn text-primary" id="btnNominalPas">
                                            <small>PAS</small>
                                        </button>
                                        <input type="text" autocomplete="off" name="nominal"
                                            class="form-control text-right @error('nominal') is-invalid @enderror" 
                                            id="nominal_payment" readonly required>
                                    </div>

                                    <div id="nominal_max_alert"></div>

                                    @error('nominal')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" autocomplete="off" name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        id="description_payment" placeholder="Angsuran pertama.." required>
                                    @error('description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="form-group text-end">
                                <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            </div>
                            <hr>

                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Tagihan</th>
                                        <th>Kode Invoice/PO</th>
                                        <th>No.Tagihan Supplier</th>
                                        <th>Tagihan</th>
                                        <th>Retur</th>
                                        <th>Dibayar</th>
                                        <th>Sisa Hutang</th>
                                        <th></th>

                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($invoice as $in)
                                        <tr>

                                            <td>{{ $in->invoice_date }}</td>
                                            <td>
                                                {{ $in->code }} <br>
                                                {{ $in->po_code }}
                                            </td>
                                            <td>{{ $in->invoice_number }}</td>
                                            <td>{{ decimalToRupiahView($in->total_invoice) }}</td>
                                            <td>{{ decimalToRupiahView($in->nominal_returned) }}</td>
                                            <td>{{ decimalToRupiahView($in->nominal_paid) }}</td>
                                            <td>{{ decimalToRupiahView($in->total_invoice - $in->nominal_paid) }}</td>

                                            <td style="text-align: center;">
                                                <button type="button" class="btn btn-sm btn-outline-primary payment-button rounded-2 px-3"
                                                    data-id="{{ $in->id }}" data-max="{{ $in->total_invoice - $in->nominal_paid }}"
                                                    id="purchase_invoice_id">

                                                    <i class="fas fa-check me-1 icon-check" style="display: none;"></i>
                                                    <span class="button-text">Pilih</span>
                                                </button>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


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
            $('#timePayment').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);
        window.setInterval(function() {
            $('#timeNowPayment').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);


        $(document).ready(function () {
            let maxPayment = 0; // ← simpan global di sini

            // Event klik invoice
            $('body').on('click', '.payment-button', function () {
                $('#nominal_max_alert').empty();

                let id = $(this).data('id');
                maxPayment = $(this).data('max');

                $('#id_invoice').val(id);
                $('#nominal_payment').attr('readonly', false).val('');

                $('#nominal_max_alert').append(
                    "<small class='text-red'> Maksimal Nominal: " + formatRupiah(maxPayment.toString(), 'Rp.') + " </small>"
                );

                // UI feedback
                $('.payment-button').removeClass('btn-primary').addClass('btn-outline-primary');
                $('.payment-button .button-text').text('Pilih');
                $('.payment-button .icon-check').hide();

                $(this).removeClass('btn-outline-primary').addClass('btn-primary');
                $(this).find('.button-text').text('Dipilih');
                $(this).find('.icon-check').show();
            });

            // Format input ketika diketik
            $('#nominal_payment').on('keyup', function () {
                let raw = $(this).val().replace(/\D/g, '');
                let val = parseInt(raw || 0);

                if (val > maxPayment) {
                    $('#nominal_max_alert').html(
                        "<div class='text-danger mt-1'><small>Nominal melebihi batas maksimal (" + formatRupiah(maxPayment.toString(), 'Rp.') + ")</small></div>"
                    );
                } else {
                    $('#nominal_max_alert').empty();
                    $(this).val(formatRupiah(raw, ''));
                }
            });


            // Tombol PAS
            $('#btnNominalPas').on('click', function () {
                if (maxPayment > 0) {
                    $('#nominal_payment').val(formatRupiah(maxPayment.toString(), ''));
                }
            });

            // Fungsi format ke Rupiah
            function formatRupiah(angka, prefix) {
                var number_string = angka.replace(/[^,\d]/g, '').toString(),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix == undefined ? rupiah : (rupiah ? prefix + rupiah : '');
            }
        });

    </script>
@endpush
