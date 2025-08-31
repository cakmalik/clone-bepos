<a href="#" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#retur-sales-void{{ $sales->id }}">
    <i class="fas fa-times me-2"></i>
    Batal
</a>

<div class="modal fade modal-blur" id="retur-sales-void{{ $sales->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="icon mb-2 text-danger icon-lg"
                     width="32" height="32" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor" fill="none"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M10 10l4 4m0 -4l-4 4"/>
                </svg>
                <h3>Pembatalan Retur</h3>
                <div class="text-secondary">
                    Apakah Anda yakin ingin <strong>membatalkan retur penjualan</strong> ini?
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Tutup</button>
                        </div>
                        <div class="col">
                            <form action="{{ url('/retur-sales', $sales->id) }}" method="POST">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger w-100">
                                    Ya, Batalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
