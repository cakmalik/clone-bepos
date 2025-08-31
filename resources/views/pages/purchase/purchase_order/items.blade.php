<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchase_order-create"
            id="purchase_order-button">
            <i class="fas fa-search me-2"></i>
            Permintaan (PR)
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_order-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Produk Permintaan (PR)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table id="tableItems"
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Barcode</th>
                                    <th>Nama Produk</th>
                                    {{-- <th>Kategori</th> --}}
                                    {{-- <th>Supplier</th> --}}
                                    {{-- <th>Merk</th> --}}
                                    <th>Satuan </th>
                                    <th>Harga</th>
                                    <th></th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchaseDetail as $pd)
                                    <tr>
                                        <td>{{ $pd->code }}</td>
                                        <td>{{ $pd->product->barcode }}</td>
                                        <td>{{ $pd->product_name }}</td>
                                        {{-- <td>{{ $pd->product->productCategory->name }}</td> --}}
                                        {{-- <td>
                                            @foreach ($pd->product->productSupplier as $supplier)
                                                @if ($supplier->supplier)
                                                    {{ $supplier->supplier->name }}<br>
                                                @endif
                                            @endforeach
                                        </td> --}}
                                        {{-- <td>
                                            @if ($pd->product->brand)
                                                {{ $pd->product->brand->name }}
                                            @else
                                                -
                                            @endif
                                        </td> --}}
                                        <td>{{ $pd->product->productUnit->name }}</td>
                                        <td>{{ rupiah($pd->product->capital_price) }}</td>
                                        <td class="text-center" style="width: 10%" id="po_product"
                                            data-id="po_product_{{ $pd->id }}">
                                            <a href="javascript:void(0)" class="btn btn-outline-primary py-2 px-3"
                                                data-id="{{ $pd->id }}" id="po_product_id">
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
