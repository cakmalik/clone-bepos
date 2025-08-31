<!-- Modal Konfirmasi Submit Retur Pembelian -->
<div class="modal fade modal-blur" id="returnConfirmModal" tabindex="-1">
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
        <h3>Konfirmasi Retur Penjualan</h3>
        <div class="text-secondary">
          Apakah Anda yakin ingin <strong>melakukan pengembalian barang</strong> ini sekarang?
        </div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <div class="row">
            <div class="col">
              <button type="button" class="btn w-100" data-bs-dismiss="modal">Batal</button>
            </div>
            <div class="col">
              <button type="button" class="btn btn-primary w-100" id="confirmReturnBtn">
                Ya, Retur Barang
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
