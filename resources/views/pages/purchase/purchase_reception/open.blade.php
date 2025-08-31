<a href="" style="margin-left:80%" class="btn btn-dark btn-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#purchase-pn-open{{ $purchase->id }}">
    Selesai
</a>
<div class="modal modal-blur fade" id="purchase-pn-open{{ $purchase->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Menyelesaikan Penerimaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin menyelesaikan penerimaan ini?</strong>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <form action="/purchase_pn_open" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $purchase->id }}">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Selesai</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
