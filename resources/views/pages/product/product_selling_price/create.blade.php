<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#product_sellingPrice-create">
            <i class="fas fa-plus me-2"></i>
            Harga Jual
        </a>
    </div>
    <div class="modal modal-blur fade" id="product_sellingPrice-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Selling Price Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productSellingPrice.create') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input name="name" type="text" autocomplete="off" class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
