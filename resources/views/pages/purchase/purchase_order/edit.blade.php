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
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card">
                    <div class="pt-3 pb-2 px-3">
                        <div class="row">
                            <div class="col-md-3">
                                <h5 class="text-muted">Kode PO</h5>
                                <h3 class="font-weight-bold text-primary">{{ $purchase->code }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Tanggal </h5>
                                <h3 class="font-weight-bold text-dark"> {{ dateWithTime($purchase->purchase_date) }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Dibuat Oleh</h5>
                                <h3 class="font-weight-bold text-dark"> {{ ucfirst(Auth()->user()->users_name) }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Supplier</h5>
                                <h3 class="font-weight-bold text-dark id="supplier_name_current">{{ $purchase->supplier->name }}</h3>
                                <h3 class="font-weight-bold text-dark id="supplier_name_update"></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="btn-list">
                            @include('pages.purchase.purchase_order.supplier_update')
                            {{-- @include('pages.purchase.purchase_order.items_update') --}}
                            @include('pages.purchase.purchase_order.cancel_order')
                            {{-- <a class="btn btn-danger" onclick="PO_Delete({{ $purchase->id }})">
                                <i class="fas fa-times me-2"></i>
                                Batal
                            </a> --}}
                        </div>

                        <form action="{{ route('purchase_order.update', $purchase->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')

                            <input type="hidden" name="code" value="{{ $purchase->code }}">
                            <input type="hidden" name="inventory_id" value="{{ $purchase->inventory_id }}">
                            <input type="hidden" name="purchase_date" value="{{ $purchase->purchase_date }}">
                            <input type="hidden" name="supplier_id_current" id="supplier_id_current"
                                value="{{ $purchase->supplier_id }}">
                            <input type="hidden" name="supplier_id_update" id="supplier_id_update">
                            <input type="hidden" name="user_id" value="{{ $purchase->user_id }}">
                            <input type="hidden" name="purchase_po_id" value="{{ $purchase->id }}">



                            <div class="row">
                                <div class="col-md-6 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga</label>
                                </div>
                                <div class="col-md-1 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>
                            <div class="row" id="items_po_update">
                            </div>
                            <div class="row" id="items_po_insert">
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-9">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-3">
                                    <h2 id="totalPO_Edit">Rp. 0</h2>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-white" id="btnPO_update">
                                    <i class="fas fa-sync me-2"></i> Perbarui
                                </button>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#purchase-po-finish{{ $purchase->id }}">
                                    <i class="fas fa-check me-2"></i> Selesaikan Pesanan
                                </a>
                            </div>

                        </form>
                        @include('pages.purchase.purchase_order.finish')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
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

            let items_current = [];


            $.ajax({
                url: "{{ url('/getDetailProductPO') }}",
                type: "GET",
                data: {
                    id: '{{ $purchase->id }}',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {

                    $.each(response, function(key, value) {
                        items_current.push(value.product_id)

                        $('#items_po_update').append(
                            "<input type='hidden' class='purchase_order_edit" + value
                            .id + "'  name='items_current[]' value='" + value
                            .product_id + "'>");

                        $('#items_po_update').append(
                            "<input type='hidden' class='purchase_order_edit" + value
                            .id + "' name='" + value.product_id + "_id' value='" +
                            value.product.id +
                            "'>");

                        $('#items_po_update').append(
                            "<div class='col-md-6 mt-3 purchase_order_edit" + value
                            .id +
                            "'><input type='text' class='form-control' name='" + value
                            .product_id + "_name' value='" + value.product_name +
                            "' readonly></div>");
                        $('#items_po_update').append(
                            "<div class='col-md-2 mt-3 purchase_order_edit" + value
                            .id +
                            "'><input type='text' class='form-control' name='" + value
                            .product_id + "_hpp' value='" + value.product.capital_price +
                            "' id='" + value.id +
                            "_hpp' readonly></div>");
                        $('#items_po_update').append(
                            "<div class='col-md-1 mt-3 qty-input purchase_order_edit" + value
                            .id +
                            "'><input type='text' min='0' class='form-control' name='" +
                            value
                            .product_id + "_qty' id='" + value.id + "_qty' value='" + value
                            .qty + "' ></div>");
                        $('#items_po_update').append(
                            "<div class='col-md-2 mt-3 purchase_order_edit" + value
                            .id +
                            "'><input type='text' min='0' class='form-control subtotal' name='" +
                            value
                            .product_id + "_subtotal' id='" + value.id +
                            "_subtotal' value='" + value.subtotal +
                            "' readonly></div>");

                        $('#items_po_update').append(
                            "<div class='col-md-1 mt-3 purchase_order_edit" + value
                            .id +
                            "'><button type='button' class='btn btn-danger' id='removeProductEditPO' data-id='" +
                            value.id + "' data-product-id='" +
                            value.id +
                            "'><li class='fas fa-trash'></li></button></div>"
                        );

                        $('body').on('click', '#removeProductEditPO', function() {
                            let id = $(this).data('id');
                            let product_id = $(this).data('product-id');
                            $('.purchase_order_edit' + id).remove();
                            items_current.remove(product_id);
                            if (items_current.length == 0 && items_other.length == 0) {
                                $('#btnPO_update').hide();
                            }
                            updateTotal()
                        });

                        updateTotal()
                    });
                }
            });

            $(document).on('focus', '.qty-input input', function () {
                $(this).select();
            });

            let PO_other = [];

            // Update Produk
            $('body').on('click', '#po_product_id_edit', function() {
                let id = $(this).data('id');
                let product_id = $(this).data('product-id');
                PO_other.push(product_id);

                if (PO_other.length > 0) {
                    $('#btnPO_update').show();
                }


                $(this).hide()
                $(this).replaceWith("<span class='badge badge-sm bg-green'>Dipilih</span>");

                $.ajax({
                    url: "{{ url('/getProduct_po_update') }}",
                    type: "POST",
                    data: {
                        PO_other: PO_other,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#items_po_insert').empty();
                        $.each(response.response, function(key, value) {

                            $('#items_po_insert').append(
                                "<input type='hidden'  name='items_other[]' class='purchase_order_edit_" +
                                value.id + "' value='" + PO_other[key] +
                                "'>");

                            $('#items_po_insert').append(
                                "<input type='hidden' class='purchase_order_edit_" +
                                value
                                .id + "' name='" + value.product_id +
                                "_id' value='" +
                                value.product.id +
                                "'>");


                            $('#items_po_insert').append(
                                "<div class='col-md-6 mt-3'><input type='text' class='form-control purchase_order_edit_" +
                                value.id + "' name='" + value.product_id +
                                "_name' value='" + value.product
                                .name + "' readonly></div>");

                            $('#items_po_insert').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control purchase_order_edit_" +
                                value.id + "' name='" + value.product_id +
                                "_hpp' value=' " + value.product
                                .capital_price +
                                "' id='" + value.id +
                                "_po_hpp' readonly></div>");

                            $('#items_po_insert').append(
                                "<div class='col-md-1 mt-3'><input type='text' class='form-control purchase_order_edit_" +
                                value.id + "' name='" + value.product_id +
                                "_qty' id='" + value.id +
                                "_po_qty' value='" + value.qty +
                                "' readonly></div>");

                            $('#items_po_insert').append(
                                "<div class='col-md-2 mt-3'><input type='text' class='form-control subtotal purchase_order_edit_" +
                                value.id + "' name='" + value.product_id +
                                "_subtotal' id='" + value.id +
                                "_po_subtotal' value='" + value
                                .subtotal + "' readonly></div>");

                            $('#items_po_insert').append(
                                "<div class='col-md-1 mt-3 purchase_order_edit_" +
                                value.id +
                                "'><a href='javascript:void(0)' id='removeProductEditPOInsert' class='btn btn-secondary btn-sm py-2 px-3' data-product-id='" +
                                value.product_id + "' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></a></div > "
                            );


                            $('body').on('click', '#removeProductEditPOInsert',
                                function() {
                                    let id = $(this).data('id');
                                    let product_id = $(this).data('product-id');
                                    $('.purchase_order_edit_' + id).remove()
                                    items_other.remove(product_id);

                                    let elemenTd = $('[data-id="po_product_edit_' +
                                        id +
                                        '"]').closest('td');

                                    if (elemenTd.length > 0) {
                                        // Buat elemen baru untuk menggantikan yang ada
                                        let linkElement =
                                            '<a href="javascript:void(0)" id="po_product_id_edit" class="btn btn-primary btn-sm py-2 px-3" data-product-id="' +
                                            value.product_id + '" data-id="' +
                                            id +
                                            '"><li class="fas fa-add"></li></a>';

                                        // Ganti elemen yang ada dengan elemen baru
                                        elemenTd.html(linkElement);
                                    }
                                    if (items_current.length == 0 &&
                                        items_other.length == 0) {
                                        $('#btnPO_update').hide();
                                    }
                                    updateTotal();
                                });

                        });
                        updateTotal();
                    }

                });

            });




            // if (PO_other.length == 0 && purchaseDetail_PO.length == 0) {
            //     $('#btnPO_update').hide();
            // }


            // Supplier



            $('body').on('click', '#supplier_id_po_update', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: "{{ url('/getDataSupplier') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#supplier_name_update').empty();
                        $('#supplier_name_current').remove();
                        $('#supplier_name_update').append(response.response.name);
                        $('#supplier_id_current').remove();
                        $('#supplier_id_update').val(response.response.id);
                    }
                });
            });
        });


        function updateTotal() {
            let totalPO_Edit = 0;

            $('.subtotal').each(function() {
                totalPO_Edit += parseFloat(this.value);
            });

            $('#totalPO_Edit').text('Rp. ' + groupNumber(totalPO_Edit));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');

            return group;
        }



        function kotak(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/reset_po') }}" + '/' + id,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                    location.reload();
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                location.reload();
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };


        // function PO_Delete(id) {

        //     Swal.fire({
        //         text: 'Apakah kamu yakin membatalkan data ini ?',
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonText: 'Yes',
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('/purchase_order') }}" + '/' + id,
        //                 type: "POST",
        //                 data: {
        //                     '_method': 'DELETE',
        //                     _token: "{{ csrf_token() }}"
        //                 },
        //                 success: function(data) {
        //                         Swal.fire('Sukses di batalkan !', data.message, 'success').then(function() {
        //                             window.location.href = '/purchase_order';
        //                         });
        //                     }

        //                     ,
        //                 error: function(data) {
        //                     Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
        //                         window.location.href = '/purchase_order';
        //                     });
        //                 }
        //             })
        //         } else {
        //             Swal.fire('Data tidak jadi batalkan', '', 'info')
        //         }
        //     });
        // };
    </script>
@endpush
