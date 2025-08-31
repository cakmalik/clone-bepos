<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary px-3 py-1 border-3" id="btnPRCreate" data-bs-toggle="modal"
            data-bs-target="#purchase_requisition-create">
            <i class="fas fa-plus me-2"></i>
            Produk
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_requisition-create" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="dataTable"
                            class="table card-table table-vcenter text-nowrap w-100
                            datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Barcode</th>
                                    {{-- <th>Kode Produk</th> --}}
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    {{-- <th>Supplier</th> --}}
                                    {{-- <th>Brand</th> --}}
                                    <th>Satuan </th>
                                    <th>Harga</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
