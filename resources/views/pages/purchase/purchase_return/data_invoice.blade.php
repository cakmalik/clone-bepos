<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchase_retur-invoice">
            <i class="fas fa-search me-2"></i>
            Faktur Pembelian
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_retur-invoice" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Faktur Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>

                                    <th>Kode Pemesanan (PO)</th>
                                    <th>No.Invoice Supplier</th>
                                    <th>Supplier</th>
                                    <th>Penerima Produk</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice as $in)
                                    <tr>

                                        <td>{{ $in->po_code }}</td>
                                        <td>{{ $in->invoice_number }}</td>
                                        <td>{{ $in->supplier }}</td>
                                        <td>{{ $in->inventory_name ?? $in->outlet_name ?? '-' }}</td>
                                        {{-- <td>
                                            <input type="radio" class="form-check-input" name="po_id"
                                                data-id="{{ $in->po_id }} " data-po-code="{{ $in->po_code }}"
                                                data-supplier="{{ $in->supplier }}"
                                                data-invoice-number="{{ $in->invoice_number }}"
                                                id="purchase_invoice_po_id" data-supplier-id="{{ $in->supplier_id }}"
                                                data-invoice-id="{{ $in->id }}">
                                        </td> --}}
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm retur-button rounded-2 px-3"
                                                data-id="{{ $in->po_id }} " data-po-code="{{ $in->po_code }}"
                                                data-supplier="{{ $in->supplier }}"
                                                data-invoice-number="{{ $in->invoice_number }}"
                                                id="purchase_invoice_po_id" data-supplier-id="{{ $in->supplier_id }}"
                                                data-invoice-id="{{ $in->id }}">
                                                <i class="fas fa-check me-1 icon-check" style="display: none;"></i>
                                                <span class="button-text">Pilih</span>
                                            </button>
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
