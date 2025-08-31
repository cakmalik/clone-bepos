@if ($show_offcanvas)
    <div style="position: fixed; right: 20px; bottom: 0px; padding: 10px; z-index: 10000; border-radius: 50%;">
        <div class="px-3" style="background-color: #bfbfbf; border-radius: 10px">
            <div class="row g-2">
                @if (($component_show ?? 'product') == 'product')
                    <div class="col">
                        <div class="input-icon mb-3">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                            </span>
                            <input type="text" class="form-control" placeholder="Cari Produk atau Barcode..."
                                wire:model.debounce.500ms="product_search" />

                        </div>
                    </div>
                @endif
                <div class="col-auto">
                    @if (($component_show ?? 'product') == 'product')
                        <a href="javascript:void(0)" wire:click="$set('product_search', '')" class="btn btn-icon mb-3"
                            aria-label="Button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M18 6l-12 12" />
                                <path d="M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                    @isset($component_show)
                        @if ($component_show == 'product')
                            <a href="javascript:void(0)" wire:click="$set('component_show','category')"
                                class="btn btn-icon mb-3" aria-label="Button">
                                Kategori
                            </a>
                        @else
                            <a href="javascript:void(0)" wire:click="$set('component_show','product')"
                                class="btn btn-icon mb-3" aria-label="Button">
                                Produk
                            </a>
                        @endif
                    @endisset
                </div>
            </div>
        </div>
    </div>
@endif
<div style="position: fixed; right: 20px; bottom: 20px; padding: 10px; z-index: 10000; border-radius: 50%;">
    @if (!$show_offcanvas)
        <button class="btn btn-icon btn-primary" wire:click="toggleOffCanvas">
            <i class="fa-solid fa-layer-group"></i> &nbsp; Produk
        </button>
    @endif
</div>
