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
                        Permintaan Pembelian
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="card">
                    <div class="pt-3 pb-2 px-3">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="text-muted">Kode PR</h5>
                                <h3 class="font-weight-bold text-primary">{{ $purchase->code }}</h3>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Dibuat Oleh</h5>
                                <h3 class="font-weight-bold text-dark"> {{ ucfirst(Auth()->user()->users_name) }}</h3>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Tanggal</h5>
                                <h3 class="font-weight-bold text-dark" id="time_update_PR"></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="btn-list">
                            @include('pages.purchase.purchase_requisition.items_update')
                            @include('pages.purchase.purchase_requisition.destroy')
                        </div>

                        <form action="{{ route('purchase_requisition.update', $purchase->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')

                            <input type="hidden" name="code" value="{{ $purchase->code }}">
                            <input type="hidden" name="code_pd" value="{{ $purchase_cpd->code }}">
                            <input type="hidden" name="purchase_date" value="{{ $purchase->purchase_date }}">
                            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                            <input type="hidden" name="user_id" value="{{ $purchase->user_id }}">
                        
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
                        
                            <div class="row" id="items_update"></div>
                            <div class="row" id="items_insert"></div>
                        
                            <div class="row mt-3">
                                <div class="col-md-9">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-3">
                                    <h2 id="totalPR_Edit">Rp. 0</h2>
                                </div>
                            </div>
                        
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="submit" class="btn btn-white" name="action" value="update">
                                    <i class="fas fa-sync me-2"></i> Perbarui
                                </button>
                        
                                <button type="submit" class="btn btn-primary" name="action" value="finish">
                                    <i class="fas fa-check me-2"></i> Selesaikan Permintaan
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
        $(function() {

            $(document).on("keyup", '#dataTable_filter input', function() {
                table.draw();
            });

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                order: false,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.product = $('#dataTable_filter input').val();
                    }
                },

                columns: [{
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                    }
                ]
            });
        });
    </script>


    <script>
        window.setInterval(function() {
            $('#time_update_PR').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);
        window.setInterval(function() {
            $('#timePR_update').val(moment().format('Y-M-D H:mm:ss'))
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




            let product = [];

            let items_other = [];
            let items_current = [];


            $.ajax({
                url: "{{ url('/getDetailProductPR') }}",
                type: "POST",
                data: {
                    id: '{{ $purchase->id }}',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {

                    $.each(response, function(key, value) {

                        items_current.push(value.product_id)

                        $('#items_update').append(
                            "<input type='hidden' class='purchase_requisition_edit" + value
                            .product_id + "'  name='items_current[]' value='" + value
                            .product_id + "'>");

                        $('#items_update').append(
                            "<input type='hidden' class='purchase_requisition_edit" + value
                            .product_id + "' name='" + value.product_id + "_id' value='" +
                            value.product.id +
                            "'>");

                        $('#items_update').append(
                            "<input type='hidden' class='purchase_requisition_edit" + value
                            .product_id + "' name='" + value.product_id +
                            "_purchase_id' value='" + value
                            .purchase_id + "'>");

                        $('#items_update').append(
                            "<div class='col-md-6 mt-3 purchase_requisition_edit" + value
                            .product_id +
                            "'><input type='text' class='form-control' name='" + value
                            .product_id + "_name' value='" + value.product_name +
                            "' readonly></div>");
                        $('#items_update').append(
                            "<div class='col-md-2 mt-3 purchase_requisition_edit" + value
                            .product_id +
                            "'><input type='text' class='form-control' name='" + value
                            .product_id + "_hpp' value='" + value.product.capital_price +
                            "' id='" + value.id +
                            "_hpp' readonly></div>");
                        $('#items_update').append(
                            "<div class='col-md-1 mt-3 qty-input purchase_requisition_edit" + value
                            .product_id +
                            "'><input type='number' min='0' class='form-control' name='" +
                            value
                            .product_id + "_qty' id='" + value.id + "_qty' step='any' value='" + value
                            .qty + "'></div>");
                        $('#items_update').append(
                            "<div class='col-md-2 mt-3 purchase_requisition_edit" + value
                            .product_id +
                            "'><input type='text' min='0' class='form-control subtotal' name='" +
                            value
                            .product_id + "_subtotal' id='" + value.id +
                            "_subtotal' value='" + value.subtotal +
                            "' readonly></div>");

                        $('#items_update').append(
                            "<div class='col-md-1 mt-3 purchase_requisition_edit" + value
                            .product_id +
                            "'><button type='button' class='btn btn-danger' id='removeProductEdit' data-id='" +
                            value.id + "' data-product-id='" +
                            value.product_id +
                            "'><li class='fas fa-trash'></li></button></div>"
                        );

                        $('body').on('click', '#removeProductEdit', function() {
                            let id = $(this).data('id');
                            let product_id = $(this).data('product-id');
                            $('.purchase_requisition_edit' + product_id).remove();
                            items_current.remove(product_id);
                            if (items_current.length == 0 && items_other.length == 0) {
                                $('#btnPR_update').hide();
                            }
                            updateTotal()
                        });

                        $('#' + value.id + '_qty').on('change', function() {
                            $('#' + value.id + '_subtotal').val($('#' + value.id +
                                    '_hpp').val() * $(this)
                                .val());
                            updateTotal()
                        });
                        $('#' + value.id + '_qty').on('keyup', function() {
                            $('#' + value.id + '_subtotal').val($('#' + value.id +
                                    '_hpp').val() * $(this)
                                .val());
                            updateTotal()
                        })
                        updateTotal();
                    });
                }
            });

            $(document).on('focus', '.qty-input input', function () {
                $(this).select();
            });



            $('body').on('click', '#items_product_update', function() {
                let id_ot = $(this).data('id');
                items_other.push(id_ot);
                $(this).hide()
                $(this).replaceWith(
                    "<span class='badge badge-sm bg-green'>Dipilih</span>");

                if (items_current.length == 0) {
                    $('#btnPR_update').show();

                }

                $.ajax({
                    url: "{{ url('/getProduct_update') }}",
                    type: "POST",
                    data: {
                        items_other: items_other,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#items_insert').empty()

                        $.each(response.response, function(key, value) {
                            $('#items_insert').append(
                                "<input type='hidden' class='purchase_requisition_edit" +
                                value.id +
                                "'  name='items_other[]' value='" +
                                items_other[key] + "'>");

                            $('#items_insert').append(
                                "<input type='hidden' class='form-control purchase_requisition_edit" +
                                value.id + "' name='" + value.id +
                                "_id' value='" + value.id + "'>");

                            $('#items_insert').append(
                                "<div class='col-md-6 mt-3 purchase_requisition_edit" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id +
                                "_name' value='" + value.name +
                                "' readonly></div>");

                            $('#items_insert').append(
                                "<div class='col-md-2 mt-3 purchase_requisition_edit" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id +
                                "_hpp' value='" + value.capital_price +
                                "' id='" +
                                value.id + "_hpp_edit' readonly></div>");

                            $('#items_insert').append(
                                "<div class='col-md-1 mt-3 qty-input purchase_requisition_edit" +
                                value.id +
                                "'><input type='number' min='0' class='form-control' name='" +
                                value.id +
                                "_qty' id='" + value.id +
                                "_qty_edit' step='any' value='0'></div>");

                            $('#items_insert').append(
                                "<div class='col-md-2 mt-3 purchase_requisition_edit" +
                                value.id +
                                "'><input type='text' min='0' class='form-control subtotal' name='" +
                                value.id +
                                "_subtotal' id='" + value.id +
                                "_subtotal_edit' value='0' readonly></div>");


                            $('#items_insert').append(
                                "<div class='col-md-1 mt-3 purchase_requisition_edit" +
                                value.id +
                                "'><button type='button' class='btn btn-secondary' id='removeProductEditInsert' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></a></div>"
                            );

                            $('body').on('click', '#removeProductEditInsert',
                                function() {
                                    let id = $(this).data('id');
                                    $('.purchase_requisition_edit' + id).remove()
                                    items_other.remove(id);

                                    let elemenTd = $('[data-id="pr_product_edit_' +
                                        id +
                                        '"]').closest('td');

                                    if (elemenTd.length > 0) {
                                        // Buat elemen baru untuk menggantikan yang ada
                                        let linkElement =
                                            '<a href="javascript:void(0)" id="items_product_update" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                                            id +
                                            '"><li class="fas fa-add"></li></a>';

                                        // Ganti elemen yang ada dengan elemen baru
                                        elemenTd.html(linkElement);
                                    }
                                    if (items_current.length == 0 &&
                                        items_other.length == 0) {
                                        $('#btnPR_update').hide();
                                    }
                                    updateTotal();
                                });



                            $('#' + value.id + '_qty_edit').on('change',
                                function() {

                                    $('#' + value.id + '_subtotal_edit').val(
                                        parseInt($('#' + value.id +
                                                '_hpp_edit')
                                            .val()) * parseInt($(this)
                                            .val()));
                                    updateTotal()
                                });
                            $('#' + value.id + '_qty_edit').on('keyup',
                                function() {
                                    $('#' + value.id + '_subtotal_edit').val(
                                        parseInt($('#' + value.id +
                                                '_hpp_edit')
                                            .val()) * parseInt($(this)
                                            .val()));
                                    updateTotal()
                                })
                        });
                        updateTotal()
                    }

                });

                $(document).on('focus', '.qty-input input', function () {
                    $(this).select();
                });

                $('#btnPR').show();
            });

        });




        function updateTotal() {
            let totalPR_Edit = 0;

            $('.subtotal').each(function() {
                totalPR_Edit += parseFloat(this.value);
            });

            $('#totalPR_Edit').text('Rp. ' + groupNumber(totalPR_Edit));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');

            return group;
        }


        //delete purchase
        function purchaseDelete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin membatalkan data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/purchase_requisition') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Batalkan !', data.message, 'success').then(
                                    function() {
                                        window.location.href = '/purchase_requisition';
                                    });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(
                                function() {
                                    window.location.href = '/purchase_requisition';
                                });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi di batalkan', '', 'info')
                }
            });
        };
    </script>
@endpush
