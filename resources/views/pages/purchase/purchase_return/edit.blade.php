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
                        Retur Pembelian
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
                            <div class="col-md-3">
                                <h5 class="text-muted">Kode Retur</h5>
                                <h3 class="font-weight-bold text-primary">{{ $purchase_retur->code }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Kode Pemesanan (PO)</h5>
                                <h3 class="font-weight-bold text-dark">{{ $purchase_retur->ref_code }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">No. Invoice Supplier</h5>
                                <h3 class="font-weight-bold text-dark">{{ $invoice_supplier->invoice_number }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Penerima Produk</h5>
                                <h3 class="font-weight-bold text-dark" >{{ $purchase_retur->inventory->name ?? $purchase_retur->outlet->name ?? '-' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card py-3">
                    <div class="card-body py-3">
                        {{-- <div class="btn-list">
                            <a class="btn btn-danger" onclick="RETUR_Delete({{ $purchase_retur->id }})">
                                <i class="fas fa-times me-2"></i>Batal
                            </a>
                        </div> --}}
                        @include('pages.purchase.purchase_return.destroy')

                        <form action="{{ route('purchase_return.update', $purchase_retur->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')
                            <input type="hidden" name="retur_id" value="{{ $purchase_retur->id }}">
                            <div class="row">
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>

                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Qty Diterima</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Telah Diretur</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">+ Qty</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>

                            <div class="row mb-4" id="product_retur">
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-2">
                                    <h2 id="totalPurchase">Rp. 0</h2>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-white">
                                    <i class="fa fa-sync me-2"></i> Sinkronkan Data
                                </button>

                                <a href="" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#purchase-retur-finish{{ $purchase_retur->id }}">
                                    <i class="fas fa-check me-2"></i>
                                    Selesai
                                </a>
                            </div>

                        </form>

                        @include('pages.purchase.purchase_return.finish')
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            $.ajax({
                url: "{{ url('/getDetailProductRT') }}",
                type: "POST",
                data: {
                    id: '{{ $PO->id }}',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $.each(response, function(key, value) {

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                            value.id + "_retur_name_edit' value='" + value.product_name +
                            "' readonly></div>"
                        );

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                            value.id + "_retur_accepted_qty' value='" + Math.floor(value
                                .accepted_qty) +
                            "' id='" + value.id +
                            "_retur_accepted_qty_edit' readonly></div>"
                        );

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                            value.id + "_returned_qty' id='" + value.id +
                            "_returned_qty_edit' value='" + Math.floor(value.returned_qty) +
                            "' readonly></div>"
                        );

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'>" +
                                "<input type='number' class='form-control' name='" + value.id + "_retur_qty' " +
                                "id='" + value.id + "_retur_qty_edit' value='0' min='0' max='" + 
                                Math.floor(value.accepted_qty - value.returned_qty) + "' " +
                                "onfocus='this.select()'>" +
                            "</div>"
                        );

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                            value.id + "_price' id='" + value.id + "_price_edit' value='" +
                            value.price +
                            "' readonly></div>"
                        );

                        let initialSubtotal = parseInt(value.price) * (parseInt($('#' + value
                            .id + '_retur_qty_edit').val()) + Math.floor(value
                            .returned_qty));

                        $('#product_retur').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control subtotal' name='" +
                            value.id + "_subtotal' id='" + value.id +
                            "_subtotal_edit' value='" +
                            initialSubtotal +
                            "' readonly></div>"
                        );

                        $('#' + value.id + '_retur_qty_edit').on('input', function() {
                            let qty = parseInt($(this).val()) ||
                                0;
                            let price = parseInt($('#' + value.id + '_price_edit')
                                .val()) || 0;
                            let returnedQty = Math.floor(value.returned_qty);

                            let newSubtotal = price * (qty + returnedQty);
                            $('#' + value.id + '_subtotal_edit').val(newSubtotal);

                            updateTotal();
                        });

                        updateTotal();
                    });
                }

            });
        })

        // function RETUR_Delete(id) {

        //     Swal.fire({
        //         text: 'Apakah kamu yakin membatalkan data ini ?',
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes',
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('/purchase_return') }}" + '/' + id,
        //                 type: "POST",
        //                 data: {
        //                     '_method': 'DELETE',
        //                     _token: "{{ csrf_token() }}"
        //                 },
        //                 success: function(data) {
        //                     Swal.fire('Sukses di Batalkan !', data.message, 'success').then(function() {
        //                         window.location.href = '/purchase_return';
        //                     });
        //                 },
        //                 error: function(data) {
        //                     Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
        //                         window.location.href = '/purchase_return';
        //                     });
        //                 }
        //             })
        //         } else {
        //             Swal.fire('Data tidak jadi batalkan', '', 'info')
        //         }
        //     });
        // };

        function updateTotal() {
            let totalPurchase = 0;

            $('.subtotal').each(function() {
                totalPurchase += parseFloat(this.value);
            });

            $('#totalPurchase').text('Rp. ' + groupNumber(totalPurchase));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');

            return group;
        }
    </script>
@endpush
