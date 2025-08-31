<div class="modal modal-blur fade" id="po-journal-retur" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pesanan Pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table
                    class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                    <thead>
                        <tr>

                            <th>Kode Invoice</th>
                            <th>No.Invoice Supplier</th>
                            <th>Supplier</th>
                            <th>Hutang</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase_invoices as $p)
                            @if (
                                $p->purchase->supplier_id == $purchase->supplier_id and
                                    $nominal->purchase_detail_sum_subtotal < $p->nominal - $p->nominal_paid)
                                <tr>
                                    <td>{{ $p->code }}</td>
                                    <td>{{ $p->invoice_number }}</td>
                                    <td>{{ $p->purchase->supplier->name }}</td>
                                    <td>{{ rupiah($p->nominal - $p->nominal_paid) }}</td>

                                    <td style="width: 3%">
                                        <input type="radio" class="form-check-input" name="purchase_invoices_id"
                                            data-code="{{ $p->code }}" data-id="{{ $p->id }}"
                                            data-invoice-supplier="{{ $p->invoice_number }}"
                                            data-nominal="{{ $p->nominal - $p->nominal_paid }}"
                                            id="purchase_invoices_journal_{{ $p->id }}">
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
