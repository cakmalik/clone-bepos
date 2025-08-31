<div class="btn-group" role="group">
    <svg xmlns="http://www.w3.org/2000/svg" class="text-green cursor-pointer" title="Tambah Kategori" data-bs-toggle="modal"
        data-bs-target="#create-sub{{ $pc->id }}" class="icon icon-tabler icon-tabler-circle-plus" width="24"
        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
        <path d="M9 12l6 0"></path>
        <path d="M12 9l0 6"></path>
    </svg>

    <div class="modal modal-blur fade" id="create-sub{{ $pc->id }}" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sub Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productCategory-sub.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="parent_id" id="parent" class="form-select">
                                    @foreach ($dataProductCategories as $cp)
                                        <option value="{{ $cp->id }}"
                                            @if ($pc->id === $cp->id) selected @endif>{{ $cp->name }}
                                        </option>
                                    @endforeach
                                    <option value="{{ $pc->id }}">{{ $pc->name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama Sub kategori</label>
                                <input name="name" type="text" autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Desc</label>
                                <textarea name="desc" type="text" rows="5" autocomplete="off"
                                    class="form-control @error('desc') is-invalid @enderror" id="desc"></textarea>
                                @error('desc')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Tambah</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
