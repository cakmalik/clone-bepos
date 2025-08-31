<div class="modal modal-blur fade" id="purchase-po-finish{{ $purchase->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
        <button type="button" class="btn-close m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="modal-status bg-primary"></div>
        <div class="modal-body text-center py-4">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon mb-2 text-primary icon-lg"
            width="32"
            height="32"
            viewBox="0 0 24 24"
            stroke-width="2"
            stroke="currentColor"
            fill="none"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <circle cx="12" cy="12" r="9" />
            <path d="M9 12l2 2l4 -4" />
          </svg>
          <h3>Selesaikan Pesanan</h3>
          <div class="text-secondary">
            Apakah Anda yakin ingin menyelesaikan pesanan pembelian ini?
          </div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col">
                <button type="button" class="btn w-100" data-bs-dismiss="modal">Tutup</button>
              </div>
              <div class="col">
                <form action="/purchase_po_finish" method="post">
                  @csrf
                  <input type="hidden" name="id" value="{{ $purchase->id }}">
                  <button type="submit" class="btn btn-primary w-100">Selesai</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  