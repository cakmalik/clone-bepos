<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}

    {{-- <div class="d-flex col-12">
                <input type="text" class="form-control col-6">
                <input type="text" class="form-control col-6">
            </div> --}}

    @if ($form_open)
        <livewire:product.tiered-price.form :product="$product" :outlet_id="$outlet_id" :as_new_price="$as_new_price" />
        {{-- <livewire:product.tiered-price.form :product="$product" /> --}}
    @else
        <div class="d-flex  align-items-center justify-content-between align-items-center mb-3 gap-3">
            <input type="text" wire:model.debounce.500ms="search" placeholder="Cari Nama produk atau Barcode"
                class="form-control">
            <select class="form-select" aria-label="Default select example" wire:model="filter">
                <option value="all">Semua </option>
                <option value="tiered">Harga Bertingkat</option>
                <option value="non_tier">Belum ada harga bertingkat</option>
            </select>
            <select class="form-select" aria-label="Default select example" wire:model="filter_outlet">
                <option value="">-- Semua Outlet --</option>
                @foreach ($outlets as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>

        </div>
        <div class="d-flex  align-items-center justify-content-between align-items-center mb-3 gap-3">
            Jumlah data : {{ number_format($count) }}
        </div>

        <table class="table no-datatable" id="dataTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Barcode</th>
                    <th>Nama Produk</th>
                    <th>Outlet</th>
                    <th>Min Qty</th>
                    <th>Max Qty</th>
                    <th>Satuan</th>
                    <th>Harga</th>
                    <th>Diperbarui</th>
                    <th colspan="2" style="width: 12%"></th>
                </tr>
            </thead>
            {{-- <tbody>
                @forelse ($data as $key => $i)
                    @php
                        $count_of_tieres = $i->tieres?->count() ?? 0;
                    @endphp

                    @if ($count_of_tieres > 0)
                        @foreach ($i->tieres as $tierKey => $tier)
                                @if ($filter_outlet != '')
                                    @if ($tier->outlet_id != $filter_outlet)
                                        @continue
                                    @endif
                                @endif
                            <tr>
                                @if ($tierKey === 0)
                                    <td rowspan="{{ $count_of_tieres }}">
                                        {{ $data->firstItem() + $key }}
                                    </td>
                                    <td rowspan="{{ $count_of_tieres }}">{{ $i->barcode }}</td>
                                    <td rowspan="{{ $count_of_tieres }}">{{ $i->name }}</td>
                                @endif
                                <td>{{ $tier->outlet->name ?? 'Semua Outlet' }}</td>
                                <td>{{ $tier->min_qty }}</td>
                                <td>{{ $tier->max_qty }}</td>
                                <td>{{ rupiah($tier->price) }}</td>
                                <td>{{ \Carbon\Carbon::parse($tier->updated_at)->translatedFormat('d/m/Y H:i') }}</td>
                                <td class="text-end d-flex gap-2" style="vertical-align: middle;">
                                    <button wire:click="select({{ $i->id }},{{ $tier->outlet_id }})"
                                        class="btn btn-info" id="detail_product" data-detail="{{ $i->id }}">
                                        Edit
                                    </button>
                                </td>
                                @if ($tierKey === 0)
                                    <td rowspan="{{ $count_of_tieres }}">

                                        <button wire:click="addNewPrice({{ $i->id }})" class="btn btn-success"
                                            id="detail_product" data-detail="{{ $i->id }}">
                                            Tambah
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <!-- Tampilkan baris untuk produk yang tidak memiliki tieres -->
                        <tr>
                            <td>{{ $data->firstItem() + $key }}</td>
                            <td>{{ $i->barcode }}</td>
                            <td>{{ $i->name }}</td>
                            <td colspan="6" class="text-end">
                                <button class="btn btn-success"
                                    wire:click="select({{ $i->id }})">Tambah</button>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse

            </tbody> --}}

            <tbody>
                @forelse ($data as $key => $i)
                    @php
                        // Filter tiers berdasarkan outlet_id
                        $filteredTiers =
                            $i->tieres?->filter(function ($tier) use ($filter_outlet) {
                                return $filter_outlet == '' || $tier->outlet_id == $filter_outlet;
                            }) ?? collect();

                        $count_of_tieres = $filteredTiers->count();
                    @endphp

                    @if ($count_of_tieres > 0)
                        @php $isFirstRow = true; @endphp
                        @foreach ($filteredTiers as $tier)
                            <tr>
                                @if ($isFirstRow)
                                    <td rowspan="{{ $count_of_tieres }}">
                                        {{ $data->firstItem() + $key }}
                                    </td>
                                    <td rowspan="{{ $count_of_tieres }}">{{ $i->barcode }}</td>
                                    <td rowspan="{{ $count_of_tieres }}">{{ $i->name }}</td>
                                    @php $isFirstRow = false; @endphp
                                @endif

                                <td>{{ $tier->outlet->name ?? 'Semua Outlet' }}</td>
                                <td>{{ $tier->min_qty }}</td>
                                <td>{{ $tier->max_qty }}</td>
                                <td>{{ $i->productUnit?->symbol ?? 'Pcs' }}</td>
                                <td>{{ rupiah($tier->price) }}</td>
                                <td>{{ \Carbon\Carbon::parse($tier->updated_at)->translatedFormat('d/m/Y H:i') }}</td>
                                <td class="text-end d-flex gap-2" style="vertical-align: middle;">
                                    <button wire:click="select({{ $i->id }},{{ $tier->outlet_id }})"
                                        class="btn btn-outline-dark" id="detail_product"
                                        data-detail="{{ $i->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>

                                @if ($loop->first)
                                    <td rowspan="{{ $count_of_tieres }}">
                                        <button wire:click="addNewPrice({{ $i->id }})" class="btn btn-primary"
                                            id="detail_product" data-detail="{{ $i->id }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <!-- Tampilkan baris untuk produk yang tidak memiliki tieres -->
                        <tr>
                            <td>{{ $data->firstItem() + $key }}</td>
                            <td>{{ $i->barcode }}</td>
                            <td>{{ $i->name }}</td>
                            <td colspan="6" class="text-end">
                                <button class="btn btn-success"
                                    wire:click="select({{ $i->id }})">Tambah</button>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

        <x-load-more :data="$data" />
    @endif

</div>
