<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-success btn-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#paymentMethod-edit{{ $pm->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>

    <div class="modal modal-blur fade" id="paymentMethod-edit{{ $pm->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('paymentMethod.update', $pm->id) }}" method="post">
                        @csrf
                        @method('put')
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input name="name_payment" type="text" autocomplete="off" class="form-control @error('name_payment') is-invalid @enderror" value="{{ $pm->name }}">
                                @error('name_payment')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Biaya Transaksi</label>
                                <input name="transaction_fees" type="text" autocomplete="off" class="form-control @error('transaction_fees') is-invalid @enderror" value="{{ $pm->transaction_fees }}">
                                @error('transaction_fees')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
