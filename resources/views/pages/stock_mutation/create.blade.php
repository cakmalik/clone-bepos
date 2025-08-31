@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Create Stock Mutation
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
                <form action="{{ route('stockMutation.store') }}" method="POST">
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Buat Stok Mutasi Baru</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <span> Kode PO </span>
                                    <h3>Auto-generate</h3>
                                </div>
                                <div class="col-md-3">
                                    <span>Dari</span>
                                    <select id="select-inventory-source" class="form-control select2"
                                        name="inventory_source_id" required>
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($myInventories as $inventory)
                                            <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <span>Ke</span>
                                    <select id="select-inventory-destination" class="form-control select2"
                                        name="inventory_destination_id" required>
                                        <option value="" disabled selected>Pilih</option>
                                        @foreach ($inventories as $inventory)
                                            <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <span>Dibuat Oleh </span>
                                    <h3>{{ Auth::user()->users_name }}</h3>
                                </div>
                            </div>

                            <hr>

                            <form action="{{ route('stockMutation.store') }}" method="post">
                                @csrf
                                <div class="btn-list">
                                    @include('pages.stock_mutation.items')
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Kode Produk</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Nama Produk</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Qty Stok</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Qty</label>
                                    </div>

                                </div>

                                <div id="items_sm"></div>
                                <button type="submit" class="btn btn-primary mt-3" id="btnSM"
                                    style="margin-left:90%">Simpan
                                </button>
                            </form>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
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

        $(document).ready(function() {
            $('.select2').select2();
            $('#btnSM').hide();
            $('#stock_mutation-button').hide();
            let sm_product = [];

            $('#select-inventory-source').on('change', function() {
                const inventory_id = $(this).val();
                $('#select-inventory-destination option').each(function() {
                    const destination = $(this).val();
                    if (!destination || destination == inventory_id) {
                        $(this).attr('disabled', true);
                    } else {
                        $(this).attr('disabled', false);
                    }
                })
                $.ajax({
                    url: `{!! route('stockMutation.getProducts') !!}?inventory_id=${inventory_id}`,
                    success: function(response) {
                        $('#stock_mutation-create table').DataTable({
                            destroy: true,
                            data: response,
                            serverside: false,
                            columns: [{
                                    data: row => row.product.id,
                                    name: 'product_id'
                                },
                                {
                                    data: row => row.product.code,
                                    name: 'product_code'
                                },
                                {
                                    data: row => row.product.name,
                                    name: 'product_name'
                                },
                                {
                                    data: row => row.product.product_unit.name,
                                    name: 'product_unit_name'
                                },
                                {
                                    data: row => row.stock_current,
                                    name: 'stock_current'
                                },
                                {
                                    data: row => `<a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3" data-id="${row.id}" id="sm_product_id_${row.id}">
                                            <li class="fas fa-add"></li>
                                        </a>`,
                                    name: 'action'
                                },
                            ]
                        })
                        $.each(response, function(key, pd) {
                            $('body').on('click', `#sm_product_id_${pd.id}`,
                                function() {
                                    const savedQty = Array(...$(
                                        '.stock_mutation_item').map(
                                        function() {
                                            return {
                                                key: $(this).find(
                                                    '.stock_mutation_product_id'
                                                ).val(),
                                                qty: $(this).find(
                                                    '.stock_mutation_qty'
                                                ).val()
                                            }
                                        })).reduce((a, c) => {
                                        a['stock_mutation_item_' + c.key] =
                                            c.qty;
                                        return a
                                    }, {})

                                    let id = $(this).data('id');
                                    sm_product.push(id);
                                    $(`#sm_product_id_${pd.id}`).parent().html(
                                        `<span class="badge badge-sm bg-green" id="sm_product_id_${pd.id}">Dipilih</span>`
                                    );

                                    $.ajax({
                                        url: `{!! route('stockMutation.getProducts') !!}?inventory_id=${inventory_id}&products_id=${sm_product.join(',')}`,
                                        type: "GET",
                                        success: function(response) {
                                            $('#items_sm').empty()

                                            $.each(response, function(
                                                key, value) {
                                                const
                                                    stockClass =
                                                    `stock_mutation_item_${value.id}`
                                                $('#items_sm')
                                                    .append(
                                                        `<div class='row stock_mutation_item' data-stock-mutation-item-class='stock_mutation_item_${value.id}'>` +
                                                        `<input type='hidden' class='stock_mutation_item_${value.id} stock_mutation_product_id' name='items[${key}][product_id]' value='${sm_product[key]}'>` +
                                                        `<div class='col-md-3 mt-3'><input type='text' class='form-control stock_mutation_item_${value.id}' value='${value.product.code}' readonly></div>` +
                                                        `<div class='col-md-3 mt-3'><input type='text' class='form-control stock_mutation_item_${value.id}' value='${value.product.name}' readonly></div>` +
                                                        `<div class='col-md-3 mt-3'><input type='number' class='form-control stock_mutation_item_${value.id}' value='${value.stock_current}' readonly></div>` +
                                                        `<div class='col-md-2 mt-3'><input type='number' class='form-control stock_mutation_qty stock_mutation_item_${value.id}' name='items[${key}][qty]' value="${savedQty[stockClass] || 1}" max="${value.stock_current}"></div>` +
                                                        `<div class='col-md-1 mt-3 stock_mutation_item_${value.id}'><button class='btn btn-secondary' id='stock_mutation_item_${value.id}'><li class='fas fa-trash'></li></button></div>` +
                                                        `</div>`
                                                    );

                                                $(`#stock_mutation_item_${value.id}`)
                                                    .on('click',
                                                        function(
                                                            e) {
                                                            $(`.stock_mutation_item_${value.id}`)
                                                                .parent()
                                                                .remove();
                                                            sm_product
                                                                .remove(
                                                                    value
                                                                    .id
                                                                );

                                                            $(`#sm_product_id_${value.id}`)
                                                                .parent()
                                                                .html(
                                                                    `<a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3" data-id="${value.id}" id="sm_product_id_${value.id}">
                                                        <li class="fas fa-add"></li>
                                                    </a>`
                                                                );
                                                            if (sm_product
                                                                .length ===
                                                                0
                                                            ) {
                                                                $('#btnSM')
                                                                    .hide();
                                                            }
                                                        });
                                            });
                                        }
                                    });
                                });
                        });
                        $('#btnSM').show();
                        $('#stock_mutation-button').show();
                    }
                })
            })
        })
    </script>
@endpush
