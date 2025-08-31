<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-outline-dark" data-bs-toggle="modal"
            data-bs-target="#customer-category-edit{{ $cc->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>

    <div class="modal modal-blur fade" id="customer-category-edit{{ $cc->id }}" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customerCategory.update', $cc->id) }}" method="post" class="form-detect-unsaved">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama *</label>
                                <input name="name" type="text" autocomplete="off" value="{{ $cc->name }}"
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
                                    class="form-control @error('description') is-invalid @enderror" id="description">{{ $cc->description }}</textarea>
                                @error('description')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Perbarui</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
