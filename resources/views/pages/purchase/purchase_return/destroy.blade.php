<a href="javascript:void(0)" class="btn btn-danger" data-bs-toggle="modal"
    data-bs-target="#purchaseReturn-delete{{ $purchase_retur->id }}">
    <i class="fas fa-times me-2"></i>
    Batal
</a>

<div class="modal fade modal-blur" id="purchaseReturn-delete{{ $purchase_retur->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close m-3" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <!-- Ikon serupa (alert-triangle) untuk menandakan pembatalan -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="32" height="32" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <path d="M12 9v4" />
          <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
          <path d="M12 16h.01" />
        </svg>
        <h3>Pembatalan Retur (RP)</h3>
        <div class="text-secondary">
          Apakah Anda yakin ingin <strong>membatalkan Retur Pembelian</strong>
          <strong>{{ $purchase_retur->code }}</strong> ini?
        </div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col">
              <button type="button" class="btn w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
            <div class="col">
              <form action="{{ url('/purchase_return', $purchase_retur->id) }}" method="POST">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-danger w-100">
                  Batalkan
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
