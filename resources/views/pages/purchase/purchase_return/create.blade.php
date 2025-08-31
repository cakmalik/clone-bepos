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
                                <h3 class="font-weight-bold text-primary">
                                    {{ purchaseReturnCode() }}
                                </h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Kode Pemesanan (PO)</h5>
                                <h3 class="font-weight-bold text-dark" id="purchase_retur_po_code"></h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Supplier</h5>
                                <h3 class="font-weight-bold text-dark" id="purchase_retur_supplier"></h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">No. Invoice</h5>
                                <h3 class="font-weight-bold text-dark" id="purchase_retur_invoice_number"></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="btn-list">
                            @include('pages.purchase.purchase_return.data_invoice')
                            @include('pages.purchase.purchase_return.data_product')

                        </div>
                        <form action="{{ route('purchase_return.store') }}" method="post" id="purchaseReturnForm" class="form-detect-unsaved">
                            @csrf
                            <input type="hidden" name="purchase_date" id="timeNowRetur">
                            <input type="hidden" name="code" value="{{ purchaseReturnCode() }}">
                            <input type="hidden" name="supplier_id" id="retur_supplier_id">
                            <input type="hidden" name="invoice_id" id="retur_invoice_id">
                            <input type="hidden" name="po_code" id="retur_po_code">
                            <input type="hidden" id="purchase_invoice_po_id">

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
                                    <label class="form-label">Qty</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga Beli</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>

                            <div class="row mb-4" id="retur_items">
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-2">
                                    <h2 id="totalPurchase">Rp. 0</h2>
                                </div>
                            </div>


                            <div class="d-flex justify-content-end gap-2 mt-3 d-none" id="btnAction">
                                <button type="submit" class="btn btn-light" name="action" value="draft">
                                    <i class="fas fa-save me-2"></i> Simpan Draf
                                </button>

                                <button type="button" class="btn btn-primary" id="triggerConfirmModal">
                                        <i class="fas fa-undo me-2"></i> Buat Retur
                                </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.purchase.purchase_return.return_confirm')

@endsection
@push('scripts')
    <script>

        let pn_id = [];

        function toggleElementVisibilityById(id, show = true) {
            const el = document.getElementById(id);
            if (!el) return;
            
            if (show) {
                el.classList.remove('d-none');
                el.style.display = '';
            } else {
                el.classList.add('d-none');
                el.style.display = 'none';
            }
        }  

        window.setInterval(function() {
            $('#timeRetur').html(moment().format('DD MMMM Y hh:mm:ss'))
        }, 1000);

        window.setInterval(function() {
            $('#timeNowRetur').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);

        $(document).ready(function() {

            toggleElementVisibilityById('btnAction', false);

            // let pn_id = [];

            $('body').one('click', '#purchase_invoice_po_id', function() {
                let id = $(this).data('id');
                let invoice_id = $(this).data('invoice-id');
                let po_code = $(this).data('po-code');
                let supplier = $(this).data('supplier');
                let invoice_number = $(this).data('invoice-number');
                let supplier_id = $(this).data('supplier-id');
                $('#purchase_retur_supplier').empty()
                $('#purchase_retur_po_code').empty()
                $('#purchase_retur_invoice_number').empty()


                $.ajax({
                    url: "{{ url('/purchase_return_getdatapo') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#table-retur').empty();
                        $('#retur_items').empty();

                        console.log(response);

                        $.each(response.response, function(key, retur) {
                            
                            $('#table-retur').append("<tr><td>" + retur.code +
                                "</td><td>" + retur.product_name +
                                "</td><td>" + Math.floor(retur.accepted_qty) +
                                "</td><td>" + Math.floor(retur.returned_qty) +
                                "</td><td>" + formatRupiah(retur.price) +
                                "</td><td>" + formatRupiah(retur.subtotal) +
                                "</td><td><a href='javascript:void(0)' class='btn btn-primary py-2 px-3' data-id='" +
                                retur.id +
                                "' id='purchase_retur_id'><li class='fas fa-add'></li></a></td></tr>"
                            );

                        });
                    }

                });

                $('#retur_supplier_id').val(supplier_id)
                $('#retur_invoice_id').val(invoice_id)
                $('#purchase_retur_supplier').append(supplier);
                $('#purchase_retur_po_code').append(po_code);
                $('#retur_po_code').val(po_code);
                $('#purchase_retur_invoice_number').append(invoice_number);
                $('#purchase_retur-invoice').modal('hide');
                $('#btn-product-retur').show()
            });

            $('body').on('click', '#purchase_retur_id', function() {
                let id = $(this).data('id');
                pn_id.push(id);

                $(this).hide()
                $(this).replaceWith(
                    "<a href='javascript:void(0)' class='btn btn-danger py-2 px-3 remove-retur-item' data-id='" + id + "'><i class='fas fa-trash'></i></a>"
                );


                $.ajax({
                    url: "{{ url('/purchase_return_getdataretur') }}",
                    type: "POST",
                    data: {
                        pn_id: pn_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#retur_items').empty();
                        $.each(response.response, function(key, value) {

                            console.log(value);
                            $('#retur_items').append(
                                "<input type='hidden' name='po_id[]' value='" +
                                pn_id[key] + "'>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_retur_name' value='" +
                                value.product_name +
                                "' readonly></div>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_retur_accepted_qty' value='" +
                                Math.floor(value.accepted_qty) + "' id='" +
                                value.id + "_retur_accepted_qty' readonly></div>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_returned_qty' id='" +
                                value.id + "_returned_qty' value='" +
                                Math.floor(value.returned_qty) +
                                "' readonly></div>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'>" +
                                    "<input type='number' class='form-control' name='" + value.id + "_retur_qty' " +
                                    "id='" + value.id + "_retur_qty' value='1' min='1' max='" + Math.floor(value.accepted_qty - value.returned_qty) + "' " +
                                    "onfocus='this.select()'>" +
                                "</div>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_price' id='" +
                                value.id + "_price' value='" + value.price +
                                "' readonly></div>"
                            );

                            $('#retur_items').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control subtotal' name='" +
                                value.id + "_subtotal' id='" + value.id +
                                "_subtotal' value='" +
                                parseInt(value.price * $('#' + value.id +
                                    '_retur_qty').val()) +
                                "' readonly></div>"
                            );

                            $('#' + value.id + '_retur_qty').on('change keyup',
                                function() {
                                    updateTotal();
                                    $('#' + value.id + '_subtotal').val(
                                        $('#' + value.id + '_price').val() * $(
                                            this).val()
                                    );
                                    updateTotal();
                                });

                        });
                        updateTotal();
                    }

                });

                toggleElementVisibilityById('btnAction', true);
            });

            $('body').on('click', '.remove-retur-item', function() {
                let id = $(this).data('id');

                // Hapus item dari #retur_items
                $('#retur_items').children().each(function() {
                    let input = $(this).find('input');
                    if (input.length && input.attr('name') && input.attr('name').startsWith(id + '_')) {
                        $(this).remove();
                    }
                });

                // Hapus elemen input tersembunyi yang menyimpan id
                $('#retur_items input[type="hidden"][value="' + id + '"]').remove();

                // Hapus dari array pn_id
                pn_id = pn_id.filter(item => item !== id);

                // Kembalikan tombol +
                $('#table-retur a.remove-retur-item[data-id="' + id + '"]').replaceWith(
                    "<a href='javascript:void(0)' class='btn btn-primary btn-sm py-2 px-3' data-id='" + id + "' id='purchase_retur_id'><li class='fas fa-add'></li></a>"
                );

                updateTotal();

                // Sembunyikan tombol retur jika tidak ada item
                if (pn_id.length === 0) {
                    toggleElementVisibilityById('btnAction', false);
                }
            });


        })

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

        function formatRupiah(angka) {
            var number_string = angka.toString(),
                sisa = number_string.length % 3,
                rupiah = number_string.substr(0, sisa),
                ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return 'Rp ' + rupiah;
        }

    </script>

    <script>
        document.getElementById('triggerConfirmModal').addEventListener('click', function () {
            const confirmModal = new bootstrap.Modal(document.getElementById('returnConfirmModal'));
            confirmModal.show();
        });

        document.getElementById('confirmReturnBtn').addEventListener('click', function () {
            const form = document.getElementById('purchaseReturnForm');

            if (!document.querySelector('input[name="action"]')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'action';
                hiddenInput.value = 'finish';
                form.appendChild(hiddenInput);
            }

            form.submit();
        })
    </script>

<script>
    document.querySelectorAll('.retur-button').forEach(button => {
        button.addEventListener('click', () => {
            let id = button.getAttribute('data-id');
            let po_code = button.getAttribute('data-po-code');
            let supplier = button.getAttribute('data-supplier');
            let invoice_number = button.getAttribute('data-invoice-number');
            let supplier_id = button.getAttribute('data-supplier-id');
            let invoice_id = button.getAttribute('data-invoice-id');

            // Reset semua tombol ke default
            document.querySelectorAll('.retur-button').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
                btn.querySelector('.button-text').textContent = 'Pilih';
                btn.querySelector('.icon-check').style.display = 'none';
            });

            // Aktifkan tombol yang dipilih
            button.classList.remove('btn-outline-primary');
            button.classList.add('btn-primary');
            button.querySelector('.button-text').textContent = 'Dipilih';
            button.querySelector('.icon-check').style.display = 'inline-block';

            // Reset semua saat pilih invoice baru
            $('#retur_items').empty();
            pn_id = []; // Kosongkan array ID produk
            $('#totalPurchase').text('Rp. 0');


            // Set data ke tampilan
            $('#purchase_retur_supplier').text(supplier);
            $('#purchase_retur_po_code').text(po_code);
            $('#purchase_retur_invoice_number').text(invoice_number);
            $('#retur_supplier_id').val(supplier_id);
            $('#retur_invoice_id').val(invoice_id);
            $('#retur_po_code').val(po_code);

            // Tutup modal
            $('#purchase_retur-invoice').modal('hide');
            $('#btn-product-retur').show();

            // Ambil data item retur via AJAX
            $.ajax({
                url: "{{ url('/purchase_return_getdatapo') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#table-retur').empty();
                    $('#retur_items').empty();

                    $.each(response.response, function(key, retur) {
                        $('#table-retur').append(`
                            <tr>
                                <td>${retur.code}</td>
                                <td>${retur.product_name}</td>
                                <td>${Math.floor(retur.accepted_qty)}</td>
                                <td>${Math.floor(retur.returned_qty)}</td>
                                <td>${formatRupiah(retur.price)}</td>
                                <td>${formatRupiah(retur.subtotal)}</td>
                                <td>
                                    <a href='javascript:void(0)' class='btn btn-primary py-2 px-3' data-id='${retur.id}' id='purchase_retur_id'>
                                        <li class='fas fa-add'></li>
                                    </a>
                                </td>
                            </tr>
                        `);
                    });
                }
            });
        });
    });
</script>

@endpush
