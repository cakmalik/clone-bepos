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
                        Pesanan Pembelian
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
                                <h5 class="text-muted">Kode PO</h5>
                                <h3 class="font-weight-bold text-primary">{{ purchaseOrderCode() }}
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Tanggal</h5>
                                <h3 class="font-weight-bold text-dark" id="timePO"></h3>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Supplier</h5>
                                <h3 class="font-weight-bold text-dark" id="supplier_name"></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="row">
                            <form action="{{ route('purchase_order.store') }}" method="post" id="purchaseOrderForm" class="form-detect-unsaved">
                                @csrf
                                <div class="btn-list">
                                    @include('pages.purchase.purchase_order.supplier')
                                    @include('pages.purchase.purchase_order.items')
                                    {{-- @include('pages.purchase.purchase_order.history') --}}
                                </div>

                                <input type="hidden" name="purchase_date" id="timeNowPO">
                                <input type="hidden" name="supplier_id" id="supplier_id">
                                <input type="hidden" id="po_supplier_id">
                                
                                <div class="row">
                                    <div class="col-md-6 mt-4">
                                        <label class="form-label">Nama Produk</label>
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <label class="form-label">Harga Beli</label>
                                    </div>
                                    <div class="col-md-1 mt-4">
                                        <label class="form-label">Qty</label>
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <label class="form-label">Subtotal</label>
                                    </div>
                                </div>
                                <div class="row" id="items_po">
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-9">
                                        <h2 class="text-end">Total</h2>
                                    </div>
                                    <div class="col-md-3">
                                        <h2 id="totalPO">Rp. 0</h2>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3 d-none" id="btnAction">
                                    <button type="submit" class="btn btn-light" name="action" value="draft">
                                        <i class="fas fa-save me-2"></i> Simpan Draf
                                    </button>
                                    
                                    <button type="button" class="btn btn-primary" id="triggerConfirmModal">
                                        <i class="fas fa-plus me-2"></i> Buat Pesanan
                                    </button>
                                </div>  
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.purchase.purchase_order.po_confirm')

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

        window.setInterval(function() {
            $('#timePO').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);

        window.setInterval(function() {
            $('#timeNowPO').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);

        $(document).ready(function() {

            $('#tableItems').DataTable({
                order: false,
                serverSide: false,
                pageLength: 25,
                destroy: true,
            });

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

            $('#direct_pay').hide();
            $('#purchase_order-button').hide();
            $('#history_button').hide();

            toggleElementVisibilityById('btnAction', false);

            let po_product = [];

            $('body').on('click', '#po_product_id', function() {
                let id = $(this).data('id');
                po_product.push(id);
                $(this).hide()
                $(this).replaceWith(
                    "<span class='btn btn-primary py-2 px-3'><i class='fas fa-check text-white'></span>");
                $.ajax({
                    url: "{{ url('/getProduct_po') }}",
                    type: "POST",
                    data: {
                        po_product: po_product,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#items_po').empty()
                        $.each(response.response, function(key, value) {
                            $('#items_po').append(
                                "<input type='hidden' class='purchase_order_" +
                                value.id +
                                "'  name='items_po[]' value='" +
                                po_product[key] + "'>");

                            $('#items_po').append(
                                "<input type='hidden' class='form-control purchase_order_" +
                                value.id + "' name='" + value.id +
                                "_po_id' value='" + value.id + "'>");

                            $('#items_po').append(
                                "<div class='col-md-6 mt-3'><input type='text' class='form-control purchase_order_" +
                                value.id + "' name='" + value.id +
                                "_po_name' value='" + value
                                .product_name + "' readonly></div>");

                            $('#items_po').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control purchase_order_" +
                                value.id + "' name='" + value.id +
                                "_po_hpp' value='" + value.product
                                .capital_price +
                                "' id='" + value.id +
                                "_po_hpp' readonly></div>");

                            $('#items_po').append(
                                "<div class='col-md-1 mt-3'><input type='text' class='form-control purchase_order_" +
                                value.id + "' name='" + value.id +
                                "_po_qty' id='" + value.id +
                                "_po_qty' value='" + value.qty +
                                "' readonly></div>");

                            $('#items_po').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control subtotal purchase_order_" +
                                value.id + "' name='" + value.id +
                                "_po_subtotal' value='" + value
                                .subtotal + "' readonly></div>");

                            $('#items_po').append(
                                "<div class='col-md-1 mt-3 purchase_order_" +
                                value.id +
                                "'><button type='button' id='removeProductPO' class='btn btn-danger py-2 px-3' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></button></div > "
                            );

                            $('body').on('click', '#removeProductPO', function() {
                                let id = $(this).data('id');
                                $('.purchase_order_' + id).remove()
                                po_product.remove(id);

                                let elemenTd = $('[data-id="po_product_' + id +
                                    '"]').closest('td');

                                if (elemenTd.length > 0) {
                                    // Buat elemen baru untuk menggantikan yang ada
                                    let linkElement =
                                        '<a href="javascript:void(0)" id="po_product_id" class="btn btn-primary py-2 px-3" data-id="' +
                                        id +
                                        '"><li class="fas fa-add"></li></a>';

                                    // Ganti elemen yang ada dengan elemen baru
                                    elemenTd.html(linkElement);
                                }
                                if (po_product.length === 0) {
                                    toggleElementVisibilityById('btnAction', false);
                                }
                                updateTotal();
                            });
                        });
                        updateTotal();
                    }
                });

                toggleElementVisibilityById('btnAction', true);
                
                $('#direct_pay').show();
            });
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
                        $('#items_po').append(
                            "<input type='hidden' class='purchase_order_" +
                            value.id +
                            "'  name='items_po[]' value='" +
                            value.id + "'>");

                        $('#items_po').append(
                            "<input type='hidden' class='form-control purchase_order_" +
                            value.id + "' name='" + value.id +
                            "_po_id' value='" + value.id + "'>");

                        $('#items_po').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control purchase_order_" +
                            value.id + "' name='" + value.id +
                            "_po_name' value='" + value
                            .product_name + "' readonly></div>");

                        $('#items_po').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control purchase_order_" +
                            value.id + "' name='" + value.id +
                            "_po_hpp' value='" + value.product
                            .capital_price +
                            "' id='" + value.id +
                            "_po_hpp' readonly></div>");

                        $('#items_po').append(
                            "<div class='col-md-2 mt-3'><input type='text' class='form-control purchase_order_" +
                            value.id + "' name='" + value.id +
                            "_po_qty' id='" + value.id +
                            "_po_qty' value='" + value.qty +
                            "' readonly></div>");

                        $('#items_po').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control subtotal purchase_order_" +
                            value.id + "' name='" + value.id +
                            "_po_subtotal' value='" + value
                            .subtotal + "' readonly></div>");

                        $('#items_po').append(
                            "<div class='col-md-1 mt-3 purchase_order_" +
                            value.id +
                            "'><button type='button' id='removeProductPO' class='btn btn-danger btn-sm py-2 px-3' data-id='" +
                            value.id +
                            "'><li class='fas fa-trash'></li></button></div > "
                        );
                    });
                    updateTotal();
                }
            });

            toggleElementVisibilityById('btnAction', true);
            
            $('#direct_pay').show();
        });


        // Supplier
        $('body').on('change', '#po_supplier_id', function() {
            let id = $(this).val();

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
                    $('#supplier_id').val(response.supplier.id);
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

                    // // Cek apakah array purchases memiliki elemen
                    // if (response.purchases.length > 0) {
                    //     // Iterasi melalui elemen purchases dan tambahkan ke tabel
                    //     response.purchases.forEach(function(purchase) {
                    //         // Periksa apakah elemen memiliki properti purchase_status
                    //         if (purchase.hasOwnProperty('purchase_status')) {
                    //             // Tambahkan data ke dalam tabel
                    //             table.row.add({
                    //                 'code': purchase.code,
                    //                 'purchase_status': purchase.purchase_status
                    //             }).draw();
                    //         }
                    //     });
                    // } else {
                    //     console.log("Respons dari server: ", response);
                    // }


                    // // Mendapatkan referensi ke elemen tabel dalam modal
                    // var tableBody = $("#history_modal tbody");

                    // // Cek apakah array purchases memiliki elemen
                    // if (response.purchases.length > 0) {
                    //     // Ambil elemen pertama dari array purchases
                    //     var firstPurchase = response.purchases[0];

                    //     // Periksa apakah elemen pertama memiliki properti purchase_details
                    //     if (firstPurchase.hasOwnProperty('purchase_status')) {
                    //         // Memasukkan data ke dalam tabel
                    //         var row = "<tr>" +
                    //             "<td>" + firstPurchase.code + "</td>" +
                    //             "<td>" + firstPurchase.purchase_status + "</td>" +
                    //             "</tr>";
                    //         tableBody.append(row);
                    //     }
                    // } else {
                    //     console.log("Respons dari server: ", response);
                    // }
                }
            });

            $('#purchase_order-supplier-create').modal('hide');
            
        });

        function updateTotal() {
            let totalPO = 0;
            $('.subtotal').each(function() {
                totalPO += parseFloat(this.value);
            });
            $('#totalPO').text('Rp. ' + groupNumber(totalPO));
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
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            confirmModal.show();
        });

        document.getElementById('confirmSubmitBtn').addEventListener('click', function () {
            const form = document.getElementById('purchaseOrderForm');

            // Tambahkan input hidden "action" = finish (jika belum ada)
            if (!document.querySelector('input[name="action"]')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'action';
                hiddenInput.value = 'finish';
                form.appendChild(hiddenInput);
            }

            form.submit();
        });
    </script>

    <script>
        let selectedSupplierId = null;

        document.querySelectorAll('.supplier-button').forEach(button => {
            button.addEventListener('click', () => {
                selectedSupplierId = button.getAttribute('data-id');

                // Reset semua tombol ke default (tidak terpilih)
                document.querySelectorAll('.supplier-button').forEach(btn => {
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

                // Trigger perubahan data sesuai ID
                $('#po_supplier_id').val(selectedSupplierId).trigger('change');
            });
        });

    </script>

@endpush
