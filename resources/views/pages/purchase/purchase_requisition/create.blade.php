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
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-title mb-0">
                            Permintaan Pembelian
                        </h2>
                    
                        <div class="d-flex align-items-center gap-2">
                            <div class="card border-1 shadow-sm px-3 py-2 mb-0">
                                <h5 class="mb-0 text-muted" id="time"></h5>
                            </div>
                            
                            <div class="btn-list mb-0">
                                @include('pages.purchase.purchase_requisition.items')
                            </div>
                        </div>
                    </div>
                                       
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card py-3">
                    <div class="card-body">
                        

                        <form action="{{ route('purchase_requisition.store') }}" method="post" class="form-detect-unsaved">
                            @csrf
                            
                            {{-- inventory_id di hide untuk custom destinasi penerimaan barang --}}
                            {{-- <input type="hidden" name="code" value="{{ purchaseRequisitionCode() }}"> --}}
                            {{-- <input type="hidden" name="inventory_id" value="{{ $inventory->id }}"> --}}

                            <input type="hidden" name="purchase_date" id="timeNowPR">
                            <div class="row">
                                <div class="col-md-5 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga Beli</label>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <label class="form-label">Stok Minimal</label>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>
                            
                            <div class="row" id="items">

                            </div>

                            <div class="row mt-3">
                                <div class="col-md-9">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-3">
                                    <h2 id="totalPR">Rp. 0</h2>
                                </div>
                            </div>
                            


                            <div class="d-flex justify-content-end gap-2 mt-3 d-none" id="btnAction">
                                <button type="submit" class="btn btn-light" name="action" value="draft">
                                    <i class="fas fa-save me-2"></i> Simpan Draf
                                </button>
                                <button type="submit" class="btn btn-primary" name="action" value="finish">
                                    <i class="fas fa-plus me-2"></i> Buat Permintaan
                                </button>
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

        $(function() {

            $(document).on("keyup", '#dataTable_filter input', function() {
                table.draw();
            });

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                destroy: true,
                order: false,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.product = $('#dataTable_filter input').val();
                        console.log('Product:', d);
                    }
                },
                columns: [{
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            var words = data.split(' ').slice(0, 5).join(' ');
                            var ellipsis = data.split(' ').length > 5 ? '...' : '';
                            return '<span class="truncated-text" title="' + data + '">' + words +
                                ellipsis + '</span>';
                        }
                    },
                    {
                        data: 'product_category',
                        name: 'product_category'
                    },
                    {
                        data: 'unit_name',
                        name: 'unit_name'
                    },
                    {
                        data: 'capital_price',
                        name: 'capital_price'
                    },
                    {
                        "className": "dt-center",
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        });
    </script>
    <script>
        window.setInterval(function() {
            $('#time').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);

        window.setInterval(function() {
            $('#timeNowPR').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);

        $(document).ready(function() {
            Array.prototype.remove = function() {
                var what, a = arguments,
                    L = a.length,
                    ax;
                while (L && this.length) {
                    what = a[--L];
                    while ((ax = this.indexOf(what)) !== -1) {
                        this.splice(ax, 1);
                    }
                }
                return this;
            };

            $('#btnPR').hide();
            $('#history_button').hide();

            let items = [];

            $('body').on('click', '#items_product', function() {
                let id = $(this).data('id');
                items.push(id);
                $(this).hide()
                $(this).replaceWith(
                    "<span class='btn btn-primary py-2 px-3'><i class='fas fa-check text-white'></i></span>"
                );
                $.ajax({
                    url: "{{ url('/getProduct_PR') }}",
                    type: "POST",
                    data: {
                        items: [id],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        updateTotal();
                        console.log(response);

                        $.each(response.response, function(key, value) {
                            $('#items').append(
                                "<input type='hidden'  class='purchase_requisition_" +
                                value.id + "' name='items[]' value='" +
                                value.id + "'>");

                            $('#items').append(
                                "<input type='hidden' class='purchase_requisition_" +
                                value.id + "' name='" + value.id +
                                "_id' value='" + value.id + "'>");

                            $('#items').append(
                                "<div class='col-md-5 mt-3 purchase_requisition_" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id + "_name' value='" + value
                                .name + "' readonly ></div>");

                            $('#items').append(
                                "<div class='col-md-2 mt-3 purchase_requisition_" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id + "_hpp' value='" + value
                                .capital_price +
                                "' id='" + value.id +
                                "_hpp' readonly></div>");

                            $('#items').append(
                                "<div class='col-md-1 mt-3 purchase_requisition_" +
                                value.id +
                                "'><input type='text' class='form-control' value='" +
                                value
                                .minimum_stock +
                                "'readonly></div>");

                            $('#items').append(
                                "<div class='col-md-1 mt-3 purchase_requisition_" +
                                value.id +
                                "'><input type='number' min='0' class='form-control qty-input' name='" +
                                value.id + "_qty' id='" + value.id +
                                "_qty' step='any' value='1' required></div>");

                            $('#items').append(
                                "<div class='col-md-2 mt-3 purchase_requisition_" +
                                value.id +
                                "'><input type='text' min='0' class='form-control subtotal' name='" +
                                value.id + "_subtotal' id='" + value
                                .id +
                                "_subtotal' value='0' readonly></div>");

                            $('#items').append(
                                "<div class='col-md-1 mt-3 purchase_requisition_" +
                                value.id +
                                "'><button type='button' id='removeProduct' class='btn btn-danger py-2 px-3' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></button></div>"
                            );

                            $('body').on('click', '#removeProduct', function() {
                                let id = $(this).data('id');
                                $('.purchase_requisition_' + id).remove()
                                items.remove(id);

                                let elemenTd = $('[data-id="pr_product_' + id +
                                    '"]').closest('td');

                                if (elemenTd.length > 0) {
                                    // Buat elemen baru untuk menggantikan yang ada
                                    let linkElement =
                                        '<a href="javascript:void(0)" id="items_product" class="btn btn-outline-primary py-2 px-3" data-id="' +
                                        id +
                                        '"><li class="fas fa-add"></li></a>';

                                    // Ganti elemen yang ada dengan elemen baru
                                    elemenTd.html(linkElement);
                                }
                                if (items.length === 0) {
                                    $('#btnPR').hide();

                                }
                                updateTotal();
                            });
                            $('#' + value.id + '_qty').on('change',
                                function() {
                                    $('#' + value.id + '_subtotal').val(
                                        $('#' + value.id + '_hpp')
                                        .val() * $(this).val());
                                    updateTotal()
                                });
                            $('#' + value.id + '_qty').on('keyup',
                                function() {
                                    $('#' + value.id + '_subtotal').val(
                                        $('#' + value.id + '_hpp')
                                        .val() * $(this).val());
                                    updateTotal()
                                })

                            let initialQty = $('#' + value.id + '_qty').val();
                            let initialHpp = $('#' + value.id + '_hpp').val();
                            $('#' + value.id + '_subtotal').val(initialHpp * initialQty);

                        });
                        updateTotal()
                    }
                });

                $(document).on('focus', '.qty-input', function () {
                    this.select();
                });

                $('#btnPR').show();

                toggleElementVisibilityById('btnAction', true);
            });
        })

        function updateTotal() {
            let totalPR = 0;
            $('.subtotal').each(function() {
                totalPR += parseFloat(this.value);
            });
            $('#totalPR').text('Rp. ' + groupNumber(totalPR));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');
            return group;
        }

        // Supplier
        $('body').on('click', '#supplier_id', function() {
            let id = $(this).data('id');
            $.ajax({
                url: "{{ url('/getDataSupplier') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#supplier_name').empty();
                    $('#purchase_order-button').show();
                    $('#history_button').show();
                    $('#supplier-id').val(response.supplier.id);
                    $('#supplier_name').append(response.supplier.name);
                    // Check if DataTable is already initialized on the table
                    if ($.fn.DataTable.isDataTable('#tableko')) {
                        // If DataTable is already initialized, destroy it
                        $('#tableko').DataTable().destroy();
                    }
                    // Data that you want to add
                    var dataToAdd = [];
                    // Check if array purchases has elements
                    if (response.purchases.length > 0) {
                        // Iterate through purchases and add to dataToAdd
                        response.purchases.forEach(function(purchase) {
                            if (purchase.hasOwnProperty('purchase_status')) {
                                dataToAdd.push({
                                    'id': purchase.id,
                                    'code': purchase.code,
                                    'sum': purchase.sum,
                                    'purchase_status': purchase.purchase_status,
                                    'purchase_date': purchase.purchase_date,
                                    'purchase_type': purchase.purchase_type
                                });
                            }
                        });
                    } else {
                        console.log("Respons dari server: ", response);
                    }
                    // Initialize DataTables and add data
                    var table = $('#tableko').DataTable({
                        data: dataToAdd,
                        columns: [{
                                data: 'code',
                                title: 'Code'
                            },
                            {
                                data: 'purchase_date',
                                title: 'Tanggal Purchase',
                                render: function(data, type, row) {
                                    // Assuming data is in the format 'Y-m-d H:i:s'
                                    var formattedDate = moment(data).format(
                                        'D MMMM Y hh:mm');
                                    return formattedDate;
                                }
                            },
                            {
                                data: 'purchase_type',
                                title: 'Purchase'
                            },
                            {
                                data: 'sum',
                                title: 'Total Price'
                            },
                            {
                                data: 'purchase_status',
                                title: 'Purchase Status'
                            },
                            {
                                data: 'id',
                                title: 'Pilih Lagi',
                                render: function(data, type, row) {
                                    return '<a data-id="' + data +
                                        '" id="history_id" class="btn btn-primary btn-sm py-2 px-3"><li class="fas fa-add"></li></a> ';
                                }
                            },
                        ],
                        order: [
                            [1,
                                'desc'
                            ]
                        ]
                    });
                    table.column(2).search('Purchase Order').draw();
                }
            });
            $('#purchase_order-supplier-create').modal('hide');
            $('#btnPRCreate').hide();
            $('#btnPO').show();
        });
        //Supplier use again
        $('body').on('click', '#history_id', function() {
            let id = $(this).data('id');
            $(this).replaceWith(
                "<span class='badge badge-sm bg-green'>Dipilih</span>");
            $.ajax({
                url: "{{ url('/getDetailProductPO') }}",
                type: "GET",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log(response);
                    $('#items_po').empty()
                    $.each(response, function(key, value) {
                        $('#items').append(
                            "<input type='hidden'  class='purchase_requisition_" +
                            value.product_id + "' name='items[]' value='" +
                            value.product_id + "'>");
                        $('#items').append(
                            "<input type='hidden' class='purchase_requisition_" +
                            value.id + "' name='" + value.product_id +
                            "_id' value='" + value.product_id + "'>");

                        $('#items').append(
                            "<div class='col-md-3 mt-3 purchase_requisition_" +
                            value.product_id +
                            "'><input type='text' class='form-control' name='" +
                            value.product_id + "_name' value='" + value
                            .product_name + "' readonly ></div>");

                        $('#items').append(
                            "<div class='col-md-3 mt-3 purchase_requisition_" +
                            value.product_id +
                            "'><input type='text' class='form-control' name='" +
                            value.product_id + "_hpp' value='" + value
                            .price +
                            "' id='" + value.product_id +
                            "_hpp' readonly></div>");

                        $('#items').append(
                            "<div class='col-md-2 mt-3 purchase_requisition_" +
                            value.product_id +
                            "'><input type='number' min='0' class='form-control' name='" +
                            value.product_id + "_qty' id='" + value.product_id +
                            "_qty' value='" + value.qty + "' ></div>");

                        $('#items').append(
                            "<div class='col-md-3 mt-3 purchase_requisition_" +
                            value.product_id +
                            "'><input type='text' min='0' class='form-control subtotal' name='" +
                            value.product_id + "_subtotal' id='" + value
                            .product_id +
                            "_subtotal' value='" + value.subtotal + "' readonly></div>");

                        $('#' + value.product_id + '_qty').on('change',
                            function() {
                                $('#' + value.product_id + '_subtotal').val(
                                    $('#' + value.product_id + '_hpp')
                                    .val() * $(this).val());
                                updateTotal()
                            });
                        $('#' + value.product_id + '_qty').on('keyup',
                            function() {
                                $('#' + value.product_id + '_subtotal').val(
                                    $('#' + value.product_id + '_hpp')
                                    .val() * $(this).val());
                                updateTotal()
                            })
                    });
                    updateTotal();
                }
            });
            $('#btnPR').show();
            $('#direct_pay').show();
        });
    </script>
@endpush
