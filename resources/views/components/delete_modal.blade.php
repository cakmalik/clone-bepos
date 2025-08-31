{{-- required --}}
{{-- 1.id 2.route 3.name 4.size(normal/small) --}}

{{-- button normal --}}
@if ($size == 'normal')
    <a href="" class="btn btn-danger py-2 px-3" data-bs-toggle="modal"
        data-bs-target="#modal-delete{{ $id }}">
        <li class="fas fa-trash"></li>
    </a>
@else
    {{-- button kecil --}}
    <a href="" class="btn btn-sm btn-danger" data-bs-toggle="modal"
        data-bs-target="#modal-delete{{ $id }}">
        <li class="fas fa-trash  me-2"></li> Hapus
    </a>
@endif
<div class="modal modal-blur fade" id="modal-delete{{ $id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin hapus : {{ $name }} ini ?</strong>
                </h4>
            </div>
            <div class="modal-footer">

                <button type="button" class="btn me-1 " data-bs-dismiss="modal">Tutup</button>
                <form action="{{ $route }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
