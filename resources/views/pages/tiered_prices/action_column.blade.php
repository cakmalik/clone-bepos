@if($row->product_name != '')
<div class="btn-list d-flex justify-content-center align-self-center">
    <a href="{{ route('tiered_prices.edit', $row->product_id) }}"
        class="btn btn-success"><i class="fas fa-edit"></i></a>

    <a href="#" class="btn btn-danger"
        onclick="tpdelete({{ $row->product_id }})"><i
            class="fas fa-trash"></i></a>
</div>
@endif
