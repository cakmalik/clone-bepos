<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchase_order-supplier-update">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Supplier
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_order-supplier-update" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                            <thead>
                                <tr>

                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>No.Handphone </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier as $sp)
                                    <tr>
                                        <td>{{ $sp->code }}</td>
                                        <td>{{ $sp->name }}</td>
                                        <td>{{ $sp->address }}</td>
                                        <td>{{ $sp->phone }}</td>
                                        <td>
                                            @if ($purchase->supplier_id == $sp->id)
                                                <input type="radio" class="form-check-input" name="supplier_id"
                                                    data-name="{{ $sp->name }}" data-id="{{ $sp->id }}"
                                                    id="supplier_id_po_update" checked>
                                            @else
                                                <input type="radio" class="form-check-input" name="supplier_id"
                                                    data-name="{{ $sp->name }}" data-id="{{ $sp->id }}"
                                                    id="supplier_id_po_update">
                                            @endif

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
