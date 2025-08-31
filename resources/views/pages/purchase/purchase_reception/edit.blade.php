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
                        Purchase Reception Update
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
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Update Penerimaan Barang</h3>
                        @include('pages.purchase.purchase_reception.open')
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-md-3">
                                <span>Kode PN </span>
                                <h3>{{ $purchase->code }}</h3>
                            </div>

                            <div class="col-md-3">
                                <span>Nama Gudang </span>
                                <h3> {{ ucfirst($purchase->inventory->name) }}
                                </h3>
                            </div>
                            <div class="col-md-3">
                                <span>Dibuat Oleh </span>
                                <h3>{{ ucfirst(Auth()->user()->users_name) }}</h3>
                            </div>

                            <div class="col-md-3">
                                <span>Tanggal </span>
                                <h3>{{ $purchase->purchase_date }}</h3>
                            </div>
                            <div class="col-md-3">
                                <span>Supplier </span>
                                <h3 id="pn_supplier_name_edit">{{ $purchase->supplier->name }}</h3>
                            </div>

                        </div>

                        <hr>

                        <div class="btn-list">
                            @include('pages.purchase.purchase_reception.dataPurchaseOrder_edit')
                            <a class="btn btn-danger btn-sm py-2 px-3" onclick="PN_Delete({{ $purchase->id }})">
                                Batalkan
                            </a>
                        </div>
                        <form action="{{ route('purchase_reception.update', $purchase->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')

                            <input type="hidden" name="supplier_id_change" id="pn_supplier_id_edit">
                            <input type="hidden" name="po_id_change" id="pn_po_id_edit">
                            <input type="hidden" name="po_code_change" id="pn_po_code_edit">

                            <div class="row">
                                <div class="col-md-3  mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Harga</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>

                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>

                            <div class="row" id="po_edit">
                            </div>
                            <button type="submit" class="btn btn-success mt-5" id="btnPN"
                                style="margin-left:90%">Update</button>
                        </form>
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
                url: "{{ url('/getDetailProductPN') }}",
                type: "POST",
                data: {
                    id: '{{ $purchase->id }}',
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $.each(response, function(key, value) {
                        $('#po_edit').append(
                            "<input type='hidden' class='form-control' name='id' value='" +
                            value
                            .id + "' >");
                        $('#po_edit').append(
                            "<input type='hidden' class='form-control' name='po_id' value='" +
                            value.purchase_po_id + "' >");

                        $('#po_edit').append(
                            "<input type='hidden' class='form-control' name='supplier_id' value='" +
                            value.purchase.supplier_id + "' >");
                        $('#po_edit').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                            value
                            .id + "_pn_name' value='" + value.product_name +
                            "' readonly></div>");

                        $('#po_edit').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                            value
                            .id + "_pn_price' value='" + value.price + "' readonly> </div>");

                        $('#po_edit').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                            value
                            .id + "_pn_qty' value='" + value.qty + "' readonly></div>");

                        $('#po_edit').append(
                            "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                            value
                            .id + "_pn_subtotal' value='" + value.subtotal +
                            "' readonly></div>");
                    });

                }
            });







            $('body').on('click', '#po_id_edit', function() {
                let id = $(this).data('id');
                let supplier_id = $(this).data('supplier-id');
                let supplier_name = $(this).data('supplier-name');
                let po_code = $(this).data('code');

                $.ajax({
                    url: "{{ url('/getPO') }}",
                    type: "POST",
                    data: {
                        po_id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#po_edit').empty()
                        $('#pn_supplier_name_edit').empty()
                        $('#pn_po_id_edit').val(id);
                        $('#pn_po_code_edit').val(po_code);
                        $('#pn_supplier_id_edit').val(supplier_id);
                        $('#pn_supplier_name_edit').append(supplier_name);

                        $.each(response.response, function(key, value) {

                            $('#po_edit').append(
                                "<input type='hidden' class='form-control' name='id' value='" +
                                p_id + "' >");

                            $('#po_edit').append(
                                "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_pn_name_change' value='" + value
                                .product_name + "' readonly></div>");

                            $('#po_edit').append(
                                "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_pn_price_change' value='" + value
                                .price + "' readonly> </div>");

                            $('#po_edit').append(
                                "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_pn_qty_change' value='" + value.qty +
                                "' readonly></div>");

                            $('#po_edit').append(
                                "<div class='col-md-3 mt-3'><input type='text' class='form-control' name='" +
                                value.id + "_pn_subtotal_change' value='" + value
                                .subtotal + "' readonly></div>");
                        });
                    }
                });


            });
        })


        function PN_Delete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin membatalkan data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/purchase_reception') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Batalkan !', data.message, 'success').then(function() {
                                    window.location.href = '/purchase_reception';
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                window.location.href = '/purchase_reception';
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi Batalkan', '', 'info')
                }
            });
        };
    </script>
@endpush
