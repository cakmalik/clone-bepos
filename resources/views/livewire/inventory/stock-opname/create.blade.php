<div x-data="{ open_offcanvas: false, selectedProductIndex: 0, keep_focus: false, show_modal_finish: @entangle('confirm_finish_modal') }" x-cloak>
    {{-- bagian filter / atau tujuan opname --}}
    <style>
        .product-card:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
    </style>
    <div class="row mb-3 ">
        <div class="d-flex justify-content-end">
            @if ($edit_form && !$is_there_changes)
                <a wire:loading.remove class="btn btn-primary ms-auto" wire:click='confirmFinish' id="btnOP"><i
                        class="fas fa-check"></i> &nbsp; Selesai
                </a>
            @endif
        </div>
    </div>
    <div class="row" x-ref="form_type">
        <div class="col-md-4">
            <div class="form-group">
                <label for="type">Tipe</label>
                <select class="form-control" wire:model="selected_type" id="type">
                    <option value="inventory">GUDANG</option>
                    <option value="outlet">OUTLET</option>
                </select>
            </div>
        </div>
        @if ($selected_type == 'inventory')
            <div class="col-md-4">
                <div class="form-group">
                    <label for="inventory_id">Gudang</label>
                    <select class="form-control" wire:model="inventory_id" id="inventory_id">
                        {{-- <option value="" disabled selected> &mdash; Pilih Gudang &mdash;
                        </option> --}}
                        @foreach ($inventories as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <div class="col-md-4">
                <div class="form-group">
                    <label for="outlet_id">Outlet</label>
                    <select class="form-control" wire:model="outlet_id" id="outlet_id">
                        <option value="" disabled selected> &mdash; Pilih Outlet &mdash;
                        </option>
                        @foreach ($outlets as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-7 mt-4">
            <label class="form-label">Nama Produk</label>
        </div>
        <div class="col-md-2 mt-4">
            <label class="form-label">Qty</label>
        </div>
        <div class="col-md-2 mt-4">
            <label class="form-label">Satuan</label>
        </div>

    </div>

    <div @click.outside="keep_focus = false;">
        @foreach ($selected_products as $key => $product)
            <div class="row mb-3" x-data>
                <div class="col-md-7" @click="$refs.productInput{{ $key }}.select(); keep_focus = true"
                    x-cloak>
                    <div for="product__{{ $key }}" class=" product-card card p-2 text-uppercase">
                        {{ $product['name'] }}</div>
                </div>
                <div class="col-md-2" x-cloak>
                    <input id="product__{{ $key }}" type="text" class="form-control"
                        wire:model="selected_products.{{ $key }}.qty" x-ref="productInput{{ $key }}"
                        x-on:keydown.arrow-up.prevent="selectedProductIndex = selectedProductIndex > 0 ? selectedProductIndex - 1 : 0"
                        x-on:keydown.arrow-down.prevent="selectedProductIndex = selectedProductIndex < {{ count($selected_products) - 1 }} ? selectedProductIndex + 1 : {{ count($selected_products) - 1 }}"
                        x-on:keydown.enter.prevent="" x-focus="selectedProductIndex === {{ $key }}"
                        x-on:click="() => $refs.productInput{{ $key }}.select(); keep_focus = true"
                        x-mask:dynamic="number
                    ">
                </div>
                <div class="col-md-2">
                    {{ $product['unit'] }}
                </div>
                <div class="col-md-1">
                    <button class="btn btn-danger" wire:click="removeProduct({{ $key }})" tabindex="-1">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach
    </div>


    @if ($selected_products != [])
        <div class="card-footer text-end mt-3">
            <div class="d-flex">
                @if ($edit_form && $is_there_changes)
                    <a class="btn btn-success ms-auto" wire:click='save' id="btnOP"><i
                            class="fa-solid fa-floppy-disk"></i> &nbsp; Perbarui
                    </a>
                @endif

                @if (!$edit_form && !$is_there_changes)
                    <a class="btn btn-primary ms-auto" wire:click='save' id="btnOP"><i
                            class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                    </a>
                @endif

                @if (!$edit_form)
                    <a class="btn btn-primary ms-auto" wire:click='save' id="btnOP"><i
                            class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                    </a>
                @endif

            </div>
        </div>
    @endif

    <div class="modal modal-blur" id="modal-primary" tabindex="-1" x-show="show_modal_finish"
        :class="{ 'show': show_modal_finish }" x-bind:style="{ display: show_modal_finish ? 'block' : 'none' }"
        x-on:keydown.escape.window="show_modal_finish = false" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content shadow-lg">
                <button type="button" class="btn-close" @click="show_modal_finish = false" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div class="modal-status bg-primary"></div>
                <div class="modal-body text-center py-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="icon mb-2 text-primary icon-lg"
                        width="32"
                        height="32"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <h3>Menyelesaikan Opname</h3>
                    <div class="text-secondary">Anda yakin menyelesaikan Opname Ini? <br> Opname yang sudah
                        diselesaikan tidak dapat edit atau dibatalkan!</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col"><a href="#" class="btn w-100" data-bs-dismiss="modal"
                                    wire:click="closeConfirmFinish">
                                    Batal
                                </a></div>
                            <div class="col"><button wire:click="finish" wire:loading.attr="disabled"
                                    class="btn btn-primary w-100" data-bs-dismiss="modal">
                                    Ya, Saya Yakin
                                </button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($inventory_id || $outlet_id)
        <div x-data @click.outside="$wire.closeOffcanvas">
            @include('livewire.components.product.template')
            <livewire:components.product.search wire:key="{{ Str::random() }}" :show_offcanvas="$show_offcanvas" :query="$product_search"
                :selectedIds="$selected_ids_product" :component_show="$component_show" />
        </div>
    @endif
</div>

<script>
    window.addEventListener('updated', function(e) {
        Toast.fire({
            icon: "success",
            title: e.detail.message,
            position: "top-end",
        });
    })
    window.addEventListener('logs-null', function(e) {
        Toast.fire({
            icon: "error",
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
