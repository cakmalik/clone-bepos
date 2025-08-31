<div>

    <div class="row ">
        {{-- <div class="col-md-3">
            <span> Kode PO </span>
            <h3>Auto-generate</h3>
        </div> --}}
        <div class="col-md-3">
            <span>Tujuan :</span>
            <select class="form-control" wire:model='mutation_category'>
                <option value="">Pilih Tipe</option>
                @foreach (\App\Enums\MutationCategory::cases() as $item)
                    <option value="{{ $item }}">{{ $item->label() }}</option>
                @endforeach
            </select>
        </div>
        @if ($mutation_category)
            @if ($sources)
                <div class="col-md-3">
                    <span>Dari :</span>
                    <select class="form-control" wire:model="source_id">
                        <option value="">Pilih</option>
                        @foreach ($sources as $src)
                            <option value="{{ $src->id }}">{{ $src->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($destinations)
                <div class="col-md-3">
                    <span>Ke :</span>
                    <select class="form-control" wire:model="destination_id">
                        <option value="">Pilih</option>
                        @foreach ($destinations as $ds)
                            <option value="{{ $ds->id }}">{{ $ds->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

        @endif
    </div>

    <div class="row mt-5 mb-2">
        <div class="col-md-6 text-center">
            <strong class="form-label text-uppercase text-bold">Nama Produk</strong>
        </div>
        <div class="col-md-2 text-center">
            <strong class="form-label text-uppercase text-bold">Stok Sekarang</strong>
        </div>
        <div class="col-md-2 text-center">
            <strong class="form-label text-uppercase text-bold">Jumlah Mutasi</strong>
        </div>
        <div class="col-md-1 text-center">
            <strong class="form-label text-uppercase text-bold">Satuan</strong>
        </div>
        <div class="col-md-1 text-center">
        </div>
    </div>

    @foreach ($selected_products as $key => $product)
        <div class="row mb-1" x-data wire:key='{{ $key }}'>
            <div class="col-md-6" x-on:click="$refs.productInput{{ $key }}.select(); keep_focus = true">
                <div for="product__{{ $key }}" class=" product-card card p-2 text-uppercase">
                    {{ $product['name'] }}</div>
            </div>
            <div class="col-md-2 text-center"
                x-on:click="$refs.productInput{{ $key }}.select(); keep_focus = true">
                <div for="product__{{ $key }}" class=" product-card card p-2 text-uppercase">
                    {{ floatval($product['current_stock']) }}</div>
            </div>
            <div class="col-md-2 text-center">
                <input id="product__{{ $key }}" type="number" class="form-control text-center"
                    wire:model="selected_products.{{ $key }}.qty" wire:loading.attr="disabled"
                    wire:target="save" x-ref="productInput{{ $key }}"
                    x-on:keydown.arrow-up.prevent="selectedProductIndex = selectedProductIndex > 0 ? selectedProductIndex - 1 : 0"
                    x-on:keydown.arrow-down.prevent="selectedProductIndex = selectedProductIndex < {{ count($selected_products) - 1 }} ? selectedProductIndex + 1 : {{ count($selected_products) - 1 }}"
                    x-on:keydown.enter.prevent="" x-focus="selectedProductIndex === {{ $key }}"
                    x-on:click="() => $refs.productInput{{ $key }}.select(); keep_focus = true"
                    x-mask:dynamic="number
                    ">
                @error('selected_products.' . $key . '.qty')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-md-1 text-center">
                {{ $product['unit'] }}
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger" wire:click="removeProduct({{ $key }})" tabindex="-1">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach

    @if ($selected_products != [])
        <div class="card-footer text-end mt-3">
            <div class="d-flex">
                <button class="btn btn-primary ms-auto" wire:click='save' wire:loading.disabled id="btnOP">
                    <span class="spinner-border spinner-border-sm me-2" wire:loading wire:target="save"
                        role="status"></span>
                    <i wire:loading.remove wire:target="save" class="fa-solid fa-floppy-disk"></i>

                    &nbsp; Simpan
                </button>
            </div>
        </div>
    @endif


    @if ($source_id)
        <div x-data @click.outside="$wire.closeOffcanvas">
            @include('livewire.components.product.template')
            <livewire:components.product.search wire:key="{{ Str::random() }}" :show_offcanvas="$show_offcanvas" :query="$product_search"
                :stock_for="$stock_for" :for_id="$source_id" :selectedIds="$selected_ids_product" />
        </div>
    @endif
</div>

<script>
    window.addEventListener('error', function(e) {
        Toast.fire({
            icon: "error",
            title: e.detail.message,
            position: "top-end",
        });
    })
    window.addEventListener('success', function(e) {
        Toast.fire({
            icon: "success",
            title: e.detail.message,
            position: "top-end",
        });
    })

    document.addEventListener('alpine:init', () => {
        Alpine.directive('focus', (el, {
            value
        }) => {
            if (value) {
                el.focus();
            }
        });
    });
</script>
