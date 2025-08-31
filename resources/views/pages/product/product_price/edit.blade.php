<div class="btn-group" role="group">
    <div class="btn-list">
        <a href="" class="btn btn-success btn-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#product_price-edit{{ $pp->id }}">
            <li class="fas fa-edit"></li>
        </a>
    </div>

    <div class="modal modal-blur fade" id="product_price-edit{{ $pp->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product Price {{ $pp->product->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('productPrice.update', $pp->id) }}" method="post">
                        @csrf
                        @method('put')
                  
                  
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Type ?</label>
                                <select name="type" class="form-control  @error('type') is-invalid @enderror">
                                    @if ($pp->type == 'utama')
                                    <option value="utama">utama</option>
                                    @else
                                    <option value="lain">lain</option>
                                    @endif
                                    @if ($pp->type !== 'utama')
                                    <option value="utama">utama</option>
                                    @else
                                    <option value="lain">lain</option>
                                    @endif
                               </select>
                               @error('type')
                               <div class="alert alert-danger">{{ $message }}</div>
                               @enderror
                            </div>
                        </div>
                   
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Price *</label>
                                <input name="price" type="text" autocomplete="off" value="{{ $pp->price }}" class="form-control @error('price') is-invalid @enderror" id="price2222">
                                @error('price')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="btn-group">
                                <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>
