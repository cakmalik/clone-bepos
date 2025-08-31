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
                        Retur Penjualan
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
                                <h3 class="font-weight-bold text-primary">{{ salesReturnCode() }}
                                </h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Tanggal Retur</h5>
                                <h3 class="font-weight-bold text-dark" id="timeReturnSales"></h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Nama Outlet</h5>
                                <h3 class="font-weight-bold text-dark">{{ Str::upper($employee->outlet->name) }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">ID Transaksi</h5>
                                <h3 class="font-weight-bold text-dark" id="sale_code"></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="btn-list">
                            @include('pages.sales.retur_sales.sales')
                            @include('pages.sales.retur_sales.product')
                        </div>
                        <form action="{{ route('retur-sales.store') }}" method="post" id="returSalesForm" class="form-detect-unsaved">
                            @csrf
                            <input type="hidden" name="code" value="{{ salesReturnCode() }}">
                            <input type="hidden" name="outlet_id" value="{{ $employee->outlet_id }}">
                            <input type="hidden" name="sales_id" id="sales_id" value="">
                            <input type="hidden" id="retur_sales_id" />

                            <div class="row">
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Kode Produk</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Qty Jual</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Qty Retur</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>
                            <div class="row mb-4" id="row_sales_place">
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-2">
                                    <h2 id="totalReturSales">Rp. 0</h2>
                                </div>
                            </div>
                            {{-- <button type="submit" class="btn btn-primary mt-5" id="btnReturSales" style="margin-left:90%">
                                <i class="fa fa-save"></i>&nbsp; Simpan</button> --}}
                        
                            <div class="d-flex justify-content-end gap-2 mt-3" id="btnAction">
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

    @include('pages.sales.retur_sales.sales_return_confirm')

@endsection
@push('scripts')
    <script>

        // ini dibuat global agar dapat direset ketika sale code dipilih lagi
        let sl_id = []; 

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
            $('#timeReturnSales').html(moment().format('DD MMMM Y H:mm:ss'))
        });

        $(document).ready(function() {

            $('#salesTable').DataTable({
                order: false,
                destroy: true,
                pageLength: 25
            });

            toggleElementVisibilityById('btnAction', false);
            

            $('body').on('change', '#retur_sales_id', function() {
                let id = $(this).val();

                $.ajax({
                    url: "{{ url('/retur-sales/getData') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#table-retur-sales').empty();
                        $('#row_sales_place').empty();
                        
                        $('#sale_code').text(response.sale_code);

                        $.each(response.response, function(key, retur) {
                            console.log(retur);
                            if (retur.qty > 0) {
                                let row = "<tr>";

                                if (!retur.is_item_bundle) {
                                    row += "<td>" + retur.product.code + "</td>";
                                    row += "<td>" + retur.product_name + "</td>";
                                    row += "<td class='text-end'>" + retur.qty +
                                        "</td>";
                                    row += "<td class='text-end'>" +
                                        retur.final_price + "</td>";
                                    row += "<td class='text-end'>" +
                                        retur.subtotal + "</td>";
                                    row +=
                                        "<td><a href='javascript:void(0)' class='btn btn-primary py-2 px-3' data-id='" +
                                        retur.id +
                                        "' id='btn-product-id'><li class='fas fa-add'></li></a></td>";
                                } else {
                                    row +=
                                        "<td></td>" +
                                        "<td colspan='2' style='padding-left: 80px'>" +
                                        retur
                                        .product_name + " (" + retur.qty + " " + retur
                                        .unit_symbol + ")</td>";
                                    row +=
                                        "<td colspan='3'></td>";
                                }

                                row += "</tr>";
                                $('#table-retur-sales').append(row);
                            }
                        });

                    }
                });
                
                $('#sales_id').val(id);
                $('#retur-sales-product').modal('hide');
                $('#retur-sales').modal('hide');
            });

            $('body').on('click', '#btn-product-id', function() {
                let id = $(this).data('id');
                sl_id.push(id);

                $(this).hide()
                $(this).replaceWith(
                    "<a href='javascript:void(0)' class='btn btn-danger delete-retur-product' data-id='" + id + "'><i class='fas fa-trash'></i></a>"
                );
                $.ajax({
                    url: "{{ url('/retur-sales/getDetailSales') }}",
                    type: "POST",
                    data: {
                        sl_id: sl_id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#row_sales_place').empty()

                        $.each(response.response, function(key, value) {
                            $('#row_sales_place').append(
                                "<input type='hidden'  name='sl_id[]' value='" +
                                sl_id[key] + "'>");

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control' name='" +
                                value.id +
                                "_retur_code_sales' value='" +
                                value.product.code +
                                "' readonly></div>");

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control'  name='" +
                                value.id +
                                "_retur_product_name_sales' value='" +
                                value.product_name +
                                "' id='" + value.id +
                                "_retur_product_name_sales' readonly></div>"
                            );

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control'  name='" +
                                value.id +
                                "_retur_unit_sales' value='" +
                                value.qty +
                                "' id='" + value.id +
                                "_retur_unit_sales' readonly></div>"
                            );

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='number' class='form-control'  name='" +
                                value.id +
                                "_retur_qty_sales' id='" +
                                value
                                .id +
                                "_retur_qty_sales' value='1' min='1'max='" +
                                value.qty +
                                "' max='" + value.qty + "' min='1' required></div>"
                            );

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control'  name='" +
                                value.id +
                                "_price_sales' id='" +
                                value.id +
                                "_price_sales' value='" +
                                value
                                .final_price + "' readonly></div>"
                            );

                            $('#row_sales_place').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control subtotal' name='" +
                                value.id +
                                "_subtotal_sales' id='" +
                                value.id +
                                "_subtotal_sales' value='" +
                                parseInt(
                                    value.final_price * $('#' +
                                        value.id +
                                        '_retur_qty_sales').val()
                                ) +
                                "' readonly></div>"
                            );

                            $('#' + value.id +
                                '_retur_qty_sales').on(
                                'change',
                                function() {
                                    $('#' + value.id +
                                            '_subtotal_sales'
                                        )
                                        .val($('#' + value
                                                .id +
                                                '_price_sales'
                                            )
                                            .val() * $(this)
                                            .val());
                                    updateTotal();
                                });
                            $('#' + value.id +
                                '_retur_qty_sales').on(
                                'keyup',
                                function() {
                                    $('#' + value.id +
                                            '_subtotal_sales'
                                        )
                                        .val($('#' + value
                                                .id +
                                                '_price_sales'
                                            )
                                            .val() * $(this)
                                            .val());
                                    updateTotal();
                                });

                        });
                        updateTotal();
                    }

                });

                toggleElementVisibilityById('btnAction', true);
            });

            $('body').on('click', '.delete-retur-product', function() {
                let id = $(this).data('id');

                // Hapus ID dari array sl_id
                sl_id = sl_id.filter(function(value) {
                    return value !== id;
                });

                // Hapus elemen input dan kolom input terkait
                $('#row_sales_place')
                    .find("input[name^='" + id + "_'], div:has(input[name^='" + id + "_'])")
                    .remove();

                // Ganti  "Hapus" kembali ke tombol +
                let newButton = "<a href='javascript:void(0)' class='btn btn-primary py-2 px-3' data-id='" +
                    id + "' id='btn-product-id'><li class='fas fa-add'></li></a>";

                // Ganti isi <td> tempat berada dengan tombol +
                $(this).closest('td').html(newButton);

                updateTotal();
            });
        });

        function updateTotal() {
            let totalReturSales = 0;

            $('.subtotal').each(function() {
                totalReturSales += parseFloat(this.value);
            });

            $('#totalReturSales').text('Rp. ' + groupNumber(totalReturSales));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');

            return group;
        }
    </script>

    <script>
        document.getElementById('triggerConfirmModal').addEventListener('click', function () {
            const confirmModal = new bootstrap.Modal(document.getElementById('returnConfirmModal'));
            confirmModal.show();
        })

        document.getElementById('confirmReturnBtn').addEventListener('click', function () {
            const form = document.getElementById('returSalesForm');

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
        let selectedSalesId = null;

        document.querySelectorAll('.pilih-button').forEach(button => {
            button.addEventListener('click', () => {
                selectedSalesId = button.getAttribute('data-id');

                // Reset semua tombol ke default (tidak terpilih)
                document.querySelectorAll('.pilih-button').forEach(btn => {
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

                $('#table-retur-sales').empty();
                $('#row_sales_place').empty();
                sl_id = []; // Kosongkan array ID produk
                $('#totalReturSales').text('Rp. 0');

                // Trigger perubahan data sesuai ID
                $('#retur_sales_id').val(selectedSalesId).trigger('change');
            });
        });

    </script>

@endpush
