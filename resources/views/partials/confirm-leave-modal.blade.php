<!-- Modal Konfirmasi Saat Meninggalkan Halaman dengan Form Belum Disimpan -->
<div class="modal fade modal-blur" id="confirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <button type="button" class="btn-close m-3" id="closeModalBtn" aria-label="Close"></button>
        <div class="modal-status bg-danger"></div>
        <div class="modal-body text-center py-4">
           <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 9v4"></path>
                <path
                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                </path>
                <path d="M12 16h.01"></path>
            </svg>
          <h3>Konfirmasi Perubahan</h3>
          <div class="text-secondary">
           Apakah Anda yakin ingin meninggalkan halaman ini tanpa menyimpan?
          </div>
        </div>
        <div class="modal-footer">
          <div class="w-100">
            <div class="row">
              <div class="col">
                <button type="button" class="btn w-100" id="cancelLeaveBtn">Batal</button>
              </div>
              <div class="col">
                <button type="button" class="btn btn-danger w-100" id="confirmLeaveBtn">
                  Tinggalkan Halaman
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let isFormDirty = false;
    let pendingNavigation = null;

    // Bootstrap 5 modal instance
    const confirmModalEl = document.getElementById('confirmModal');
    const confirmModal = new bootstrap.Modal(confirmModalEl, {
      backdrop: 'static',
      keyboard: false
    });

    // Tangkap semua form yang ingin dipantau (berikan class .form-detect-unsaved)
    const forms = document.querySelectorAll('.form-detect-unsaved');

    forms.forEach(form => {
        form.addEventListener('input', () => {
            isFormDirty = true;
        });
        form.addEventListener('submit', () => {
            isFormDirty = false;
        });
    });

    // Tangkap klik link untuk navigasi
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || this.target === '_blank') {
                return;
            }
            if (isFormDirty) {
                e.preventDefault();
                pendingNavigation = href;
                confirmModal.show();
            }
        });
    });

    // Tombol konfirmasi meninggalkan halaman
    document.getElementById('confirmLeaveBtn').addEventListener('click', () => {
        confirmModal.hide();
        isFormDirty = false;
        if (pendingNavigation) {
            window.location.href = pendingNavigation;
        }
    });

    // Tombol batal meninggalkan halaman (tutup modal)
    document.getElementById('cancelLeaveBtn').addEventListener('click', () => {
        pendingNavigation = null;
        confirmModal.hide();
    });

    // Tombol close X modal juga sama fungsinya dengan batal
    document.getElementById('closeModalBtn').addEventListener('click', () => {
        pendingNavigation = null;
        confirmModal.hide();
    });

    // Tangkap refresh / tutup tab
    window.addEventListener('beforeunload', function (e) {
        if (isFormDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
