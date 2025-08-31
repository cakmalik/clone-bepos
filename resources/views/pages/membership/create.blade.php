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
            Membership
        </a>
    </div>
    <div class="modal modal-blur fade" id="product_unit-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Membership</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/membership') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama Level *</label>
                                <input name="name" type="text" autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror" id="name" required>
                                @error('name')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Point Minimal</label>
                                <input name="score_min" type="number" autocomplete="off"
                                    class="form-control @error('score_min') is-invalid @enderror" id="score_min"
                                    required>
                                @error('score_min')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Point Maksimal</label>
                                <input name="score_max" type="number" autocomplete="off"
                                    class="form-control @error('score_max') is-invalid @enderror" id="score_max"
                                    required>
                                @error('score_max')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Score Loyalty</label>
                                <input name="score_loyalty" type="text" autocomplete="off"
                                    class="form-control @error('score_loyalty') is-invalid @enderror" id="score_loyalty"
                                    required>
                                @error('score_loyalty')
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
