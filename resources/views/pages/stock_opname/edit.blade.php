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
                    {{ $title }}
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
                    <h3 class="card-title"> {{ $title }} </h3>
                    @include('pages.stock_opname.finish')
                </div>
                <div class="card-body border-bottom py-3">

                    <form action="{{ route('stockOpname.update', $stockOpname->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Tipe</label>
                                    <select class="form-control" name="type" id="type">
                                        @if ($stockOpname->inventory_id)
                                        <option value="GUDANG" selected>GUDANG</option>
                                        <option value="OUTLET">OUTLET</option>
                                        @else
                                        <option value="GUDANG">GUDANG</option>
                                        <option value="OUTLET" selected>OUTLET</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Gudang</label>
                                    <select class="form-control" name="inventory_id" id="inventory_id">
                                        <option value="" disabled selected> &mdash; Pilih Gudang &mdash;
                                        </option>

                                        @foreach ($inventory as $inv)
                                        @if ($stockOpname->inventory_id == $inv->id)
                                        <option value="{{ $inv->id }}" selected>{{ $inv->name }}
                                        </option>
                                        @else
                                        <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">Outlet</label>
                                    <select class="form-control" name="outlet_id" id="outlet_id">
                                        <option value="" disabled selected> &mdash; Pilih Outlet &mdash;
                                        </option>
                                        @foreach ($outlet as $t)
                                        @if ($stockOpname->outlet_id == $t->id)
                                        <option value="{{ $t->id }}" selected>{{ $t->name }}
                                        </option>
                                        @else
                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <hr>

                        <div class="row">
                            <div class="btn-list">
                                <button type="button" class="btn btn-primary" id="showItem">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Produk
                                </button>
                            </div>
                            @include('pages.stock_opname.items_update')
                        </div>

                        <div class="row">
                            <div class="col-md-8 mt-4">
                                <label class="form-label">Nama Produk</label>
                            </div>
                            <div class="col-md-3 mt-4">
                                <label class="form-label">Qty</label>
                            </div>

                        </div>
                        <div class="row" id="page-opname">
                            @foreach ($stockOpname->stockOpnameDetails as $op)
                            <div class="col-md-8 mt-3 stock_opname_{{ $op->product->id }}">
                                <input type="hidden" class="form-control" name="items[]" value="{{ $op->product->id }}">
                                <input type="text" class="form-control" id="{{ $op->product->id }}_name"
                                    name="{{ $op->product->id }}_name" value="{{ $op->product->name }}" readonly>
                            </div>
                            <div class="col-md-3 mt-3 stock_opname_{{ $op->product->id }}">
                                <input type="text" class="form-control" id="{{ $op->product->id }}_qty"
                                    name="{{ $op->product->id }}_qty" value="{{ $op->qty_so }}">
                            </div>
                            <div class="col-md-1 mt-3 stock_opname_{{ $op->product->id }}">
                                <button type='button' id='removeProduct_{{ $op->product->id }}'
                                    data-id='{{ $op->product->id }}' class='btn btn-secondary btn-sm py-2 px-3'>
                                    <li class='fas fa-trash'></li>
                                </button>
                            </div>
                            @endforeach
                        </div>


                        <div class="card-footer text-end mt-3">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-success ms-auto" id="btnOP"><i
                                        class="fa-solid fa-floppy-disk"></i> &nbsp; Update
                                </button>
                            </div>
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

        $(document).on("keyup",'#dataTable_filter input',function(){
            table.draw();
        });

        let items = [];

      


        $('#showItem').click(function() {
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
                        d.selected_product = items;
                    }
                },

                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'product_category', name: 'product_category'},
                    {data: 'supplier_name', name: 'supplier_name'},
                    {data: 'brand_name', name: 'brand_name'},
                    {"className": "dt-center", data: 'action', name: 'action'}

                ]
            });
            $('#itemOpname').modal('show');
        });


        let product_current = @json($product_current);

        $.each(product_current, function(key, value) {
            items.push(value);

            $('#page-opname').append(
                "<input type='hidden'  class='stock_opname_" +
                value + "' name='items[]' value='" +
                value + "'>");

            $('body').on('click', '#removeProduct_' + value, function() {
                let id = $(this).data('id');
                $('.stock_opname_' + id).remove()
                items.remove(id);
                let elemenTd = $('[data-id="opname_product_' +
                    id +
                    '"]').closest('td');

                if (elemenTd.length > 0) {
                    // Buat elemen baru untuk menggantikan yang ada
                    let linkElement =
                        '<a href="javascript:void(0)" id="items_opname" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                        id +
                        '"><li class="fas fa-add"></li></a>';

                    // Ganti elemen yang ada dengan elemen baru
                    elemenTd.html(linkElement);
                }
                if (items.length === 0) {
                    $('#btnOP').hide();

                }

            });
        })



        $('body').on('click', '#items_opname', function() {
            let id = $(this).data('id');
            items.push(id);

            $(this).hide()
            $(this).replaceWith(
                "<span class='badge badge-sm bg-green'>Dipilih</span>");

            $.ajax({
                url: "{{ url('/getProductOpname') }}",
                type: "POST",
                data: {
                    items: [id],
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $.each(response.response, function(key, value) {
                        $('#page-opname').append(
                            "<input type='hidden'  class='stock_opname_" +
                            value.id + "' name='items[]' value='" +
                            value.id + "'>");
                        $('#page-opname').append(
                            "<div class='col-md-8 mt-3 stock_opname_" +
                            value.id +
                            "'><input type='text' class='form-control' name='" +
                            value.id + "_name' value='" + value
                            .name + "' readonly ></div>");

                        $('#page-opname').append(
                            "<div class='col-md-3 mt-3 stock_opname_" +
                            value.id +
                            "'><input type='text'  class='form-control' name='" +
                            value.id + "_qty' id='" +
                            value.id + "_qty' value='0' required></div>");

                        $('#page-opname').append(
                            "<div class='col-md-1 mt-3 stock_opname_" +
                            value.id +
                            "'><button type='button' id='removeProduct' class='btn btn-secondary btn-sm py-2 px-3' data-id='" +
                            value.id +
                            "'><li class='fas fa-trash'></li></button></div>"
                        );

                        $('body').on('click', '#removeProduct', function() {
                            let id = $(this).data('id');
                            $('.stock_opname_' + id).remove()
                            items.remove(id);

                            let elemenTd = $('[data-id="opname_product_' +
                                id +
                                '"]').closest('td');

                            if (elemenTd.length > 0) {
                                // Buat elemen baru untuk menggantikan yang ada
                                let linkElement =
                                    '<a href="javascript:void(0)" id="items_opname" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                                    id +
                                    '"><li class="fas fa-add"></li></a>';

                                // Ganti elemen yang ada dengan elemen baru
                                elemenTd.html(linkElement);
                            }
                            if (items.length === 0) {
                                $('#btnOP').hide();

                            }

                        });

                        $("#" + value.id + "_qty").on("input", function() {

                            let qty = $(this).val();
                            input = qty.replace(/\D/g, '');
                            $(this).val(input);
                        });
                    });

                }

            });
            $('#btnOP').show();
        });
    });
</script>
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


            

            let type = $('#type').val();

            if (type === 'GUDANG') {
                $('#inventory_id').prop('disabled', false);
                $('#inventory_id').prop('required', true);
                $('#outlet_id').prop('required', false);
                $('#outlet_id').prop('disabled', true);
                $('#outlet_id').prop('selectedIndex', 0);
            } else {
                $('#inventory_id').prop('disabled', true);
                $('#inventory_id').prop('selectedIndex', 0);
                $('#outlet_id').prop('disabled', false);
                $('#outlet_id').prop('required', true);
                $('#inventory_id').prop('required', false);
            }

            $('#type').on('change', function() {

                if ($(this).val() === 'GUDANG') {
                    $('#inventory_id').prop('disabled', false);
                    $('#inventory_id').prop('required', true);
                    $('#outlet_id').prop('required', false);
                    $('#outlet_id').prop('disabled', true);
                    $('#outlet_id').prop('selectedIndex', 0);
                } else {
                    $('#inventory_id').prop('disabled', true);
                    $('#inventory_id').prop('selectedIndex', 0);
                    $('#outlet_id').prop('disabled', false);
                    $('#outlet_id').prop('required', true);
                    $('#inventory_id').prop('required', false);
                }
            })
        });
</script>
@endpush