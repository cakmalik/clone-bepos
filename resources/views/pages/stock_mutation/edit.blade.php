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
                        Update Stock Mutation
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
                        <div>
                            <h3 class="card-title">Edit Stok Mutasi</h3>
                        </div>
                        <div class="ms-3">
                            @if ($mutation->status == 'draft')
                                <span class="badge bg-warning">Draft</span>
                            @elseif ($mutation->status == 'open')
                                <span class="badge bg-primary">Open</span>
                            @elseif ($mutation->status == 'done')
                                <span class="badge bg-success">Done</span>
                            @elseif ($mutation->status == 'void')
                                <span class="badge bg-danger">Void</span>
                            @endif
                        </div>
                        <div class="ms-auto d-flex" style="gap: 8px;">
                            @if ($isReceiver)
                                @if ($mutation->status == 'open')
                                    <form method="POST" action="{{ route('stockMutation.receive', $mutation->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="done">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-circle-check me-2"></i>
                                            Konfirmasi Diterima
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if ($mutation->status == 'draft')
                                    <form method="POST" action="{{ route('stockMutation.updateStatus', $mutation->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="open">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>
                                            Selesai
                                        </button>
                                    </form>
                                @endif
                                @if ($mutation->status == 'done' || $mutation->status == 'draft')
                                    <form method="POST" action="{{ route('stockMutation.updateStatus', $mutation->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="void">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-close me-2"></i>
                                            Void
                                        </button>
                                    </form>
                                @endif
                            @endif

                        </div>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row">
                            <div class="col-md-3">
                                <span> Kode PO </span>
                                <h3>{{ $mutation->code }}</h3>
                            </div>
                            <div class="col-md-3">
                                <span>Dari</span>
                                <h3>{{ $mutation->inventorySource->name }}</h3>
                            </div>
                            <div class="col-md-3">
                                <span>Ke</span>
                                <h3>{{ $mutation->inventoryDestination->name }}</h3>
                            </div>
                            <div class="col-md-3">
                                <span>Dibuat Oleh </span>
                                <h3>{{ $mutation->creator->users_name }}</h3>
                            </div>
                        </div>

                        <hr>

                        <form action="{{ route('stockMutation.update', [$mutation->id]) }}" method="post">
                            @csrf
                            @method('put')
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
                            <button type="submit" class="btn btn-success mt-3" id="btnSM"
                                style="margin-left:90%">Update
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        Array.prototype.remove = function() {
            var what, a = arguments,
                L = a.length,
                ax
            while (L && this.length) {
                what = a[--L]
                while ((ax = this.indexOf(what)) !== -1) {
                    this.splice(ax, 1)
                }
            }
            return this
        }

        $(document).ready(function() {
            $('#btnSM').hide()
            $('#stock_mutation-button').hide()
            let mutationStatus = '{!! $mutation->status !!}'
            let isReceiver = @json($isReceiver);
            let selected = @json($mutation->items);
            let initQty = selected.map(item => ({
                key: item.product_id,
                qty: item.qty
            })).reduce((a, c) => {
                a['stock_mutation_item_' + c.key] = c.qty
                return a
            }, {})
            let sm_product = selected.map(item => item.product_id)

            function fetch(product_ids, savedQty) {
                $.ajax({
                    url: `{!! route('stockMutation.getProducts') !!}?inventory_id={!! $mutation->inventorySource->id !!}&products_id=${product_ids.join(',')}`,
                    type: 'GET',
                    success: function(response) {
                        $('#items_sm').empty()

                        $.each(response, function(key, value) {
                            const stockClass = `stock_mutation_item_${value.id}`
                            $('#items_sm').append(
                                `<div class='row stock_mutation_item' data-stock-mutation-item-class='stock_mutation_item_${value.id}'>` +
                                `<input type='hidden' class='stock_mutation_item_${value.id} stock_mutation_product_id' name='items[${key}][product_id]' value='${sm_product[key]}'>` +
                                `<div class='col-md-3 mt-3'><input type='text' class='form-control stock_mutation_item_${value.id}' value='${value.product.code}' readonly></div>` +
                                `<div class='col-md-3 mt-3'><input type='text' class='form-control stock_mutation_item_${value.id}' value='${value.product.name}' readonly></div>` +
                                `<div class='col-md-3 mt-3'><input type='number' class='form-control stock_mutation_item_${value.id}' value='${value.stock_current}' readonly></div>` +
                                `<div class='col-md-2 mt-3'><input type='number' class='form-control stock_mutation_qty stock_mutation_item_${value.id}' name='items[${key}][qty]' value="${savedQty[stockClass] || 1}" max="${value.stock_current}"></div>` +
                                `<div class='col-md-1 mt-3 stock_mutation_item_${value.id}'><button class='btn btn-secondary' id='stock_mutation_item_${value.id}'><li class='fas fa-trash'></li></button></div>` +
                                `</div>`,
                            )

                            $(`#stock_mutation_item_${value.id}`).on('click', function(e) {
                                $(`.stock_mutation_item_${value.id}`).parent()
                                    .remove()
                                sm_product.remove(value.id)

                                $(`#sm_product_id_${value.id}`).parent().html(
                                    `<a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3" data-id="${value.id}" id="sm_product_id_${value.id}">
                                                        <li class="fas fa-add"></li>
                                                    </a>`,
                                )
                                if (sm_product.length === 0) {
                                    $('#btnSM').hide()
                                }
                            })

                            $(`#sm_product_id_${value.id}`).parent().html(
                                `<span class="badge badge-sm bg-green" id="sm_product_id_${value.id}">Dipilih</span>`
                            )
                        })

                        if ((mutationStatus !== 'draft' && mutationStatus !== 'open') || isReceiver) {
                            $('#btnSM').hide()
                            $('#stock_mutation-button').hide()
                            $('.stock_mutation_item').each(function() {
                                $(this).find('button').hide()
                                $(this).find('.stock_mutation_qty').attr('readonly', true)
                            })
                        }
                    },
                })
            }

            $.ajax({
                url: `{!! route('stockMutation.getProducts') !!}?inventory_id={!! $mutation->inventory_source_id !!}`,
                success: function(response) {
                    $('#stock_mutation-create table').DataTable({
                        destroy: true,
                        data: response,
                        serverside: false,
                        columns: [{
                                data: row => `<a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3" data-id="${row.id}" id="sm_product_id_${row.id}">
                                            <li class="fas fa-add"></li>
                                        </a>`,
                                name: 'action',
                            },
                            {
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
                        ],
                    })
                    $.each(response, function(key, pd) {
                        $('body').on('click', `#sm_product_id_${pd.id}`, function() {
                            const savedQty = Array(...$('.stock_mutation_item').map(
                                function() {
                                    return {
                                        key: $(this).find(
                                                '.stock_mutation_product_id')
                                            .val(),
                                        qty: $(this).find('.stock_mutation_qty')
                                            .val(),
                                    }
                                })).reduce((a, c) => {
                                a['stock_mutation_item_' + c.key] = c.qty
                                return a
                            }, {})

                            let id = $(this).data('id')
                            sm_product.push(id)

                            fetch(sm_product, savedQty)
                        })
                    })
                    $('#btnSM').show()
                    $('#stock_mutation-button').show()
                },
            })

            fetch(sm_product, initQty)
        })
    </script>
@endpush
