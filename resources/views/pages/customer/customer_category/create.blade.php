<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#product_unit-create">
            <i class="fa-solid fa-plus"></i>&nbsp;
            Kategori Pelanggan
        </a>
    </div>
    <div class="modal modal-blur fade" id="product_unit-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customerCategory.create') }}" method="post" enctype="multipart/form-data" class="form-detect-unsaved">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama *</label>
                                <input name="name" type="text" autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" type="text" rows="5" autocomplete="off"
                                    class="form-control @error('description') is-invalid @enderror" id="description"></textarea>
                                @error('description')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
