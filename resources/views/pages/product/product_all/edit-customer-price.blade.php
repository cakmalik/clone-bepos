<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-none d-md-flex justify-content-between">
                        <h3 class="card-title">Harga Berdasarkan Kategori Pelanggan</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product.update-customer-price', $products->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                @if ($customerCategories->isEmpty())
                                    <div id="no-price-message"
                                        class="d-flex justify-content-center align-items-center mb-3">
                                        <p class="mb-0">Tidak ada kategori pelanggan yang tersedia.</p>
                                    </div>
                                @else
                                    <div id="price-container">
                                        <div class="row mb-3 text-start gap-4">
                                            <div class="col-2"><strong>Kategori Pelanggan</strong></div>
                                            <div class="col-2"><strong>Harga Jual</strong></div>
                                        </div>
                                        @foreach ($customerCategories as $category)
                                            @php
                                                $price = $customerPrices->firstWhere(
                                                    'customer_category_id',
                                                    $category->id,
                                                );
                                            @endphp
                                            <div class="row mb-2 text-start gap-4">
                                                <div class="col-2 mb-2">
                                                    <input type="text"
                                                        name="product_prices[{{ $loop->index }}][customer_category]"
                                                        class="form-control w-auto" value="{{ $category->name }}"
                                                        readonly />
                                                    <input type="hidden"
                                                        name="product_prices[{{ $loop->index }}][customer_category_id]"
                                                        value="{{ $category->id }}" readonly />
                                                </div>
                                                <div class="col-2 mb-2">
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="number"
                                                            name="product_prices[{{ $loop->index }}][price]"
                                                            class="form-control" placeholder="0.00"
                                                            value="{{ $price ? $price->price : '' }}"
                                                            min="0" step="0.01" />
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary ms-auto">
                                    <i class="fa-solid fa-floppy-disk px-1"></i> Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
