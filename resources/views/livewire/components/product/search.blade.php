<div x-data="{ open_offcanvas: @entangle('show_offcanvas') }" {{-- @open-offcanvas.window="open_offcanvas = true; $nextTick(()=>$refs.input.focus())" @close-offcanvas.window="open_offcanvas = false" --}}>

    <style>
        .product-card {
            cursor: pointer;
        }

        .product-card:hover {
            background-color: #f5f5f5;
        }
    </style>

    <div x-show="open_offcanvas" class="offcanvas offcanvas-end" :class="{ 'show': open_offcanvas }" tabindex="-1"
        id="offcanvasEnd" x-bind:style="{ visibility: open_offcanvas ? 'visible' : 'hidden' }">
        {{-- <div class="offcanvas-header">
           <div class="mb-3"></div>
        </div> --}}
        <div class="offcanvas-body">
            @if (isset($component_show) && $component_show == 'product')
                <div>
                    @forelse ($products as $p)
                        <div class="card p-2 product-card product-card @if ($loop->last) mb-5 @endif"
                            wire:click='addProduct({{ $p }})'>
                            <strong class="text-uppercase">{{ $p->name }}</strong>
                            <span class="text-secondary">{{ $p->barcode }}</span> <br>
                            <span class="text-secondary">{{ $p->supplier_name }}</span>
                        </div>
                    @empty
                        <p class="text-secondary">Produk Tidak Ditemukan</p>
                    @endforelse
                </div>
                <x-load-more :data="$products" />
            @else
                <div>
                    @forelse ($categories as $c)
                        <div class="card p-2 product-card product-card @if ($loop->last) mb-5 @endif"
                            wire:click='addCategory({{ $c->id }})'>
                            <strong class="text-uppercase">{{ $c->name }}</strong>
                        </div>
                    @empty
                        <p class="text-secondary">Kategori Tidak Ditemukan</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</div>
