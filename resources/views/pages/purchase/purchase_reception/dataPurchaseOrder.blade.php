<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reception-create">
            <i class="fas fa-search me-2"></i>
            Pesanan (PO)
        </a>
    </div>
    <div class="modal modal-blur fade" id="reception-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih data dari Pesanan Pembelian (PO)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
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

                                        <td style="text-align: center">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm po-button rounded-2 px-3"
                                                data-id="{{ $pd->id }}" data-code="{{ $pd->code }}" data-supplier-id="{{ $pd->supplier_id }}" data-supplier-name="{{ $pd->supplier->name }}">
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
