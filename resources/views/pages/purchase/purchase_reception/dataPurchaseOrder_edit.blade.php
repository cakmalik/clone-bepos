<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reception-edit">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Puurchase Order
        </a>
    </div>
    <div class="modal modal-blur fade" id="reception-edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih data dari Pesanan Pembelian (PO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Kode PO</th>
                                    <th>Supplier</th>
                                    <th>Tanggal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase_po as $pd)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pd->code }}</td>
                                        <td>{{ $pd->supplier->name }}</td>
                                        <td>{{ dateWithTime($pd->created_at) }}</td>
                                        <td>
                                            <input type="radio" class="form-check-input" name="po_id"
                                                data-supplier-id="{{ $pd->supplier_id }}"
                                                data-supplier-name="{{ $pd->supplier->name }}"
                                                data-id="{{ $pd->id }}" data-code="{{ $pd->code }}"
                                                id="po_id_edit">
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
