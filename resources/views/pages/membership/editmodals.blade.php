<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-success btn-sm py-2 px-3" data-bs-toggle="modal"
            data-bs-target="#editMembershipModal{{ $membership->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>
    <div class="col-auto ms-auto d-print-none">
        <div class="modal modal-blur fade" id="editMembershipModal{{ $membership->id }}" tabindex="-1"
            aria-labelledby="editMembershipModalLabel" role="dialog" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Membership</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('/membership/' . $membership->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Nama Level *</label>
                                    <input name="name" type="text" autocomplete="off"
                                        class="form-control @error('name') is-invalid @enderror" id="name"
                                        value="{{ $membership->name }}" required>
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
                                        value="{{ $membership->score_min }}" required>
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
                                        value="{{ $membership->score_max }}" required>
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
                                        value="{{ $membership->score_loyalty }}" required>
                                    @error('score_loyalty')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="btn-group">
                                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
