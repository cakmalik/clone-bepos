<div class="modal modal-blur fade" id="purchase-retur-finish{{ $purchase_retur->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Retur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin menyelesaikan pengembalian ini?</strong>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-white" data-bs-dismiss="modal">Tutup</button>
                    <form action="/purchase_return_finish" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $purchase_retur->id }}">
                        <button type="submit" class="btn btn-primary">Selesai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
