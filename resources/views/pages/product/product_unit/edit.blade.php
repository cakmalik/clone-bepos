<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-outline-dark" data-bs-toggle="modal"
            data-bs-target="#product_unit-edit{{ $pu->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>

    <div class="modal modal-blur fade" id="product_unit-edit{{ $pu->id }}" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Satuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productUnit.update', $pu->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama Satuan</label>
                                <input name="name" type="text" autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror" id="product_unit_name"
                                    value="{{ $pu->name }}">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input name="desc" type="text" autocomplete="off"
                                    class="form-control @error('desc') is-invalid @enderror" id="product_unit_desc"
                                    value="{{ $pu->desc }}">
                                @error('desc')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Perbarui</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
