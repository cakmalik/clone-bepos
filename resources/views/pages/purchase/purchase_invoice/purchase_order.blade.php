<div class="modal modal-blur fade" id="po-invoice-create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pesanan Pembelian (PO)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table
                    class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                    <thead>
                        <tr>

                            <th>Kode PO</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($PO as $p)
                            <tr>
                                <td>{{ $p->code }}</td>
                                <td>{{ Carbon\Carbon::parse($p->purchase_date)->isoFormat('D MMMM Y hh:mm') }}</td>
                                <td>{{ $p->supplier }}</td>
                                <td>{{ rupiah($p->subtotal) }}</td>

                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-sm btn-outline-primary invoice-button rounded-2 px-3"
                                        data-id="{{ $p->purchase_id }}" data-code="{{ $p->code }}"
                                        data-supplier="{{ $p->supplier }}" data-subtotal="{{ $p->subtotal }}"
                                        id="purchase_po_id">

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
