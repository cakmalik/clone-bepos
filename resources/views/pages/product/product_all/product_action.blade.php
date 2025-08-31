<a href="{{ route('product.detail', $row->id) }}" class="btn btn-outline-primary">
    <i class="fas fa-eye"></i>
</a>

<a href="{{ route('product.edit', $row->id) }}" class="btn btn-outline-dark">
    <i class="fas fa-edit"></i>
</a>

<a href="javascript:void(0)" onclick="deleteData('{{ route('product.destroy', $row->id) }}')"
    class="btn btn-outline-danger">
    <i class="fas fa-trash"></i>
</a>
{{-- @if ($row->deleted_at === null)
    <a href="{{ route('product.detail', $row->id) }}" class="btn btn-info btn-sm py-2 px-3">
        <i class="fas fa-eye"></i>
    </a>
    <div class="btn-group" role="group">
        <div class="btn-list">
            <a href="{{ route('product.edit', $row->id) }}" class="btn btn-success btn-sm py-2 px-3">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </div>
    <a href="javascript:void(0)" onclick="deleteData('{{ route('product.destroy', $row->id) }}')"
        class="btn btn-danger btn-sm py-2 px-3">
        <i class="fas fa-trash"></i>
    </a>
@else
    <a href="javascript:void(0)" class="btn btn-warning btn-sm py-2 px-3">
        <i class="fa-solid fa-recycle"></i>
    </a>
    <a href="javascript:void(0)" class="btn btn-danger btn-sm py-2 px-3">
        <i class="fa-solid fa-skull"></i>
    </a>
@endif --}}
