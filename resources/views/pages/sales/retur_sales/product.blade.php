<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#retur-sales-product"
            id="btn-product-retur-sales">
            <i class="fas fa-plus me-2"></i>
            Produk
        </a>
    </div>
    <div class="modal modal-blur fade" id="retur-sales-product" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Data Produk yang akan di Kembalikan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Produk</th>
                                    <th>Nama Produk</th>
                                    <th style="width: 5%">Qty</th>
                                    <th>Harga</th> 
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="table-retur-sales">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
