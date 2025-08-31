<div class="col-auto ms-auto d-print-none">
    <div class="modal modal-blur fade" id="brand-edit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merk Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-brand-edit" method="post">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama Merk</label>
                                <input name="name" type="text" autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror" id="brandName" required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Perbarui</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
