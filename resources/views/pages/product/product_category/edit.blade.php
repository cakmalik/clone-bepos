<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-outline-dark" data-bs-toggle="modal"
            data-bs-target="#product_unit-edit{{ $pc->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>

    <div class="modal modal-blur fade" id="product_unit-edit{{ $pc->id }}" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productCategory.update', $pc->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama *</label>
                                <input name="name" type="text" autocomplete="off" value="{{ $pc->name }}"
                                    class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Parent Category ? *</label>
                                <input name="is_parent_category" type="checkbox"
                                    class=" @error('is_parent_category') is-invalid @enderror" id="is_parent_categoryyy"
                                    onclick="enableParent2()">
                                @error('is_parent_category')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Parent *</label>
                                <select name="parent_category_id"
                                    class="form-control  @error('parent_category_id') is-invalid @enderror"
                                    id="parent_categoryyy">
                                    <option value="0">Pilih Parent</option>
                                    @foreach ($dataProductCategories as $cp)
                                        <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_category_id')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Tipe Batas</label>
                                <select name="type_margin" class="form-control" id="type_margin">
                                    <option value="0" {{ $pc->type_margin == 0 ? 'selected' : '' }}>Pilih Type
                                    </option>
                                    <option value="NOMINAL" {{ $pc->type_margin == 'NOMINAL' ? 'selected' : '' }}>
                                        Nominal</option>
                                    <option value="PERCENT" {{ $pc->type_margin == 'PERCENT' ? 'selected' : '' }}>
                                        Persen</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Batas Minimal</label>
                                <div class="input-group">
                                    <input name="minimum_margin" type="number" autocomplete="off"
                                        value="{{ $pc->minimum_margin }}" pattern="^\d+(\.\d{1,2})?%?$"
                                        class="form-control @error('minimum_margin') is-invalid @enderror"
                                        id="minimum_margin">
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
                                    class="form-control @error('desc') is-invalid @enderror" id="desc">{{ $pc->desc }}</textarea>
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
