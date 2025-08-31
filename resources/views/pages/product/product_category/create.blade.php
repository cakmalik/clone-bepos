<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#product_unit-create">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Kategori Produk
        </a>
    </div>
    <div class="modal modal-blur fade" id="product_unit-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productCategory.create') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <input name="is_parent_category" type="checkbox"
                                    class=" @error('is_parent_category') is-invalid @enderror" id="is_parent_categoryy"
                                    onclick="enableParent()"> Kategori utama
                                @error('is_parent_category')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3" id="child_wrapper">
                                <select name="parent_category_id"
                                    class="form-control  @error('parent_category_id') is-invalid @enderror"
                                    id="parent_categoryy">
                                    <option value="0">Pilih Induk</option>
                                    @foreach ($dataProductCategories as $cp)
                                        <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_category_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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
                                <label class="form-label">Tipe Batas</label>
                                <select name="type_margin" class="form-control" id="type_margin">
                                    <option value="0">Pilih Tipe</option>
                                    <option value="NOMINAL">Nominal</option>
                                    <option value="PERCENT">Persen</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Batas Minimal</label>
                                <div class="input-group">
                                    <input name="minimum_margin" type="number" autocomplete="off"
                                        pattern="^\d+(\.\d{1,2})?%?$"
                                        class="form-control @error('minimum_margin') is-invalid @enderror"
                                        id="minimum_margin" minlength="1">
                                </div>
                                @error('minimum_margin')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>




                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="desc" type="text" rows="5" autocomplete="off"
                                    class="form-control @error('desc') is-invalid @enderror" id="desc"></textarea>
                                @error('desc')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
