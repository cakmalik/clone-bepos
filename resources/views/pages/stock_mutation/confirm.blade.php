<a href="" class="btn btn-success btn-sm py-2 px-3" data-bs-toggle="modal"
    data-bs-target="#stock_mutation_confirm{{ $mutation->id }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-checklist" width="24" height="24"
        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
        stroke-linejoin="round">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
        <path d="M9.615 20h-2.615a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8"></path>
        <path d="M14 19l2 2l4 -4"></path>
        <path d="M9 8h4"></path>
        <path d="M9 12h2"></path>
    </svg>
    <span>Konfirmasi diterima</span>
</a>
<div class="modal modal-blur fade" id="stock_mutation_confirm{{ $mutation->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Mutasi Masuk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin mengkonfirmasi mutasi masuk ini?</strong>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <form action="{{ route('stockMutation.receive', $mutation->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="done">
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Konfirmasi</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
