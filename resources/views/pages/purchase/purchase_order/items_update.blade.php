<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchase_order-update"
            id="purchase_order-button_update">
            <i class="fas fa-plus me-2"></i>
            Permintaan (PR)
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_order-update" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih produk dari data Permintaan Pembelian (PR)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode PR</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Supplier</th>
                                    <th>Brand</th>
                                    <th>Satuan </th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseDetail as $pd)
                                    <tr>

                                        <td>{{ $pd->code }}</td>
                                        <td>{{ $pd->product->name }}</td>
                                        <td>{{ $pd->product->productCategory->name }}</td>
                                        <td>
                                            @foreach ($pd->product->productSupplier as $supplier)
                                                @if ($supplier && $supplier->supplier)
                                                    {{ $supplier->supplier->name }}<br>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($pd->product->brand)
                                                {{ $pd->product->brand->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $pd->product->productUnit->name }}</td>
                                        <td>{{ $pd->qty }}</td>
                                        <td>{{ rupiah($pd->product->capital_price) }}</td>
                                        <td id="po_product_edit" data-id="po_product_edit_{{ $pd->id }}">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm py-2 px-3"
                                                data-id="{{ $pd->id }}" data-product-id="{{ $pd->product->id }}"
                                                id="po_product_id_edit">
                                                <li class="fas fa-add"></li>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
