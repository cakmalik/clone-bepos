<a href="" class="btn btn-danger btn-sm py-2 px-3" data-bs-toggle="modal"
    data-bs-target="#product_unit-delete{{ $sp->id }}">
    <li class="fas fa-trash"></li>
</a>
<div class="modal modal-blur fade" id="product_unit-delete{{ $sp->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Supplier </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin hapus Supplier : {{ $sp->name }} ini ?</strong>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <form action="{{ route('supplier.destroy', $sp->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
