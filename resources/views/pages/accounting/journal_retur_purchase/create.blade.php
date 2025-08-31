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
                    <h2 class="page-title text-uppercase">
                        Jurnal Retur Pembelian
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">


            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif --}}

            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ 'KODE RETUR : ' . $purchase->code }} <br>
                        {{ 'SUPPLIER : ' . $purchase->supplier->name }} <br>
                        {{ 'NOMINAL : ' . rupiah($nominal->purchase_details_sum_subtotal) }} <br>
                    </h3>
                </div>
                <div class="card-body border-bottom py-3">

                    <form action="{{ url('accounting/journal_retur_purchase/retur') }}" method="post">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id" value="{{ $purchase->id }}">
                            <input type="hidden" name="nominal" value="{{ $nominal->purchase_details_sum_subtotal }}">
                            @if ($journal_number)
                                <input type="hidden" name="journal_number_id" value="{{ $journal_number->id }}">
                            @endif
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Alokasi Nilai Retur</label>
                                    <select name="retur_value" id="retur_value"
                                        class="form-control @error('retur_value') is-invalid @enderror">
                                        <option selected value="0" disabled> &mdash; Pilih Alokasi Nilai
                                            Retur
                                            &mdash;
                                        </option>
                                        <option value="2">Mengurangi Utang</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3" id="form_invoice">

                                <div class="col-md-3">
                                    <label class="form-label">Nomor Invoice</label>
                                    <input type="text" class="form-control" id="purchase_invoice_code" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">No.Inv Supplier</label>
                                    <input type="text" class="form-control" id="purchase_invoice_number_invoice"
                                        readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Nominal</label>
                                    <input type="text" class="form-control" id="purchase_invoice_nominal" readonly>
                                </div>
                                <div class="col-md-3">
                                    <a href="" class="btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#po-journal-retur"
                                        style="margin-top: 28px;height: 35px;width:50px;">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </a>
                                    @include('pages.accounting.journal_retur_purchase.search')
                                </div>
                            </div>

                        </div>
                        @if ($nominal->purchase_details_sum_subtotal == $total_debit)
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                Simpan Jurnal Retur</button>
                        @else
                            <button type="submit" disabled class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                Simpan Jurnal Retur</button>
                        @endif
                    </form>

                    <hr>

                    <form action="{{ url('accounting/journal_retur_purchase') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $purchase->id }}">
                                    <label class="form-label">Debit</label>
                                    <select name="journal_account_debit"
                                        class="form-control @error('journal_account_debit') is-invalid @enderror">
                                        <option selected value="0" disabled> &mdash; Pilih Debit
                                            &mdash;
                                        </option>

                                        @foreach ($jurnal_account as $jc)
                                            <option value="{{ $jc->id }}">
                                                {{ $jc->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kredit</label>
                                    <select name="journal_account_kredit"
                                        class="form-control @error('journal_account_kredit') is-invalid @enderror">
                                        <option selected value="0" disabled> &mdash; Pilih Kredit
                                            &mdash;
                                        </option>

                                        @foreach ($jurnal_account as $jc)
                                            <option value="{{ $jc->id }}">
                                                {{ $jc->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nominal</label>
                                    <input type="text" class="form-control @error('nominal') is-invalid @enderror"
                                        autocomplete="off" name="nominal" id="journal_retur_nominal">
                                    <small class="text-red">Maksimal Nominal :
                                        {{ rupiah($nominal->purchase_details_sum_subtotal - $total_debit) }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" autocomplete="off"
                                        class="form-control  @error('description') is-invalid @enderror"
                                        name="description">
                                </div>
                            </div>

                        </div>
                        <div class="form-group text-end">
                            @if ($nominal->purchase_details_sum_subtotal == $total_debit)
                                <button type="submit" disabled class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            @else
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            @endif

                        </div>
                    </form>
                </div>
            </div>



            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Jurnal Retur Pembelian</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Retur</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($journal_number != null)
                                    @foreach ($journal_number->journalTransaction as $jn)
                                        <tr>
                                            <td>{{ $journal_number->code }}</td>
                                            <td>{{ $journal_number->date }}</td>
                                            <td>{{ $jn->description }}</td>
                                            <td>
                                                @if ($jn->type == 'debit')
                                                    {{ rupiah($jn->nominal) }}
                                                @else
                                                    {{ ' ' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($jn->type == 'credit')
                                                    {{ rupiah($jn->nominal) }}
                                                @else
                                                    {{ ' ' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" class="text-end">Total</td>
                                        <td>{{ rupiah($total_debit) }}</td>
                                        <td>{{ rupiah($total_kredit) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let total_debit = @json($total_debit);
        let nominal = @json($nominal->purchase_details_sum_subtotal);

        let max = parseInt(nominal) - parseInt(total_debit);
        $('#journal_retur_nominal').on('keyup', function() {
            let value = $(this).val().replace(/[^,\d]/g, '').toString();;
            if (value > max) {
                $(this).val(' ')
            }
        });

        journal_retur_nominal.addEventListener('keyup', function(e) {
            journal_retur_nominal.value = formatRupiah(this.value, 'Rp.');
        });

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }


        let purchase_invoices = @json($purchase_invoices);

        $.each(purchase_invoices, function(key, p) {
            $('body').one('click', '#purchase_invoices_journal_' + p.id, function(event) {

                $('#purchase_invoice_code').empty();
                $('#purchase_invoice_number_invoice').empty();
                $('#purchase_invoice_nominal').empty();

                let id = $(this).data('id');
                let code = $(this).data('code');
                let invoice_supplier = $(this).data('invoice-supplier');
                let nominal = $(this).data('nominal');
                $('#purchase_invoice_code').val(code);
                $('#purchase_invoice_number_invoice').val(invoice_supplier);
                $('#purchase_invoice_nominal').val(nominal);
                $('#form_invoice').append("<input type='hidden' name='purchase_invoice_id' value='" + id +
                    "'>");

            });

        });



        $('#retur_value').on('change', function() {

            if ($(this).val() == 2) {
                $('#form_invoice').show();
            } else {
                $('#purchase_invoice_code').val('')
                $('#purchase_invoice_number_invoice').val('')
                $('#purchase_invoice_nominal').val('')
                $('input[name=purchase_invoices_id]').prop('checked', false);
                $('#form_invoice').hide();

            }
        });

        $(document).ready(function() {
            $('#form_invoice').hide();
        });
    </script>
@endpush
