<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}

    <div class="form-group">
        <label for="cari" class="mb-2">Cari Nama/Kode produk</label>
        <input wire:model="cari" type="text" class="form-control" id="cari" placeholder="Cari Nama/Kode produk">
    </div>

    <div class="table-responsive mt-3">
        <table class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 5%">Nama Produk</th>
                    <th>Barcode</th>
                    <th>Jumlah Produk</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $i)
                    <tr>
                        <td>{{ $i->name }}</td>
                        <td>
                            @if ($i->barcode)
                                {{-- <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($i->barcode, 'C39') }}"
                                    alt="barcode" /> --}}
                                {{ $i->barcode }}
                            @else
                                <span class="badge badge-danger">Belum ada barcode</span>
                            @endif

                        </td>
                        <td>{{ $i->productStock[0]?->stock_current }}</td>
                        <td>
                            @if ($i->barcode)
                                <button class="btn btn-sm btn-primary" wire:click="selectedId({{ $i }})"
                                    data-bs-toggle="modal" data-bs-target="#exampleModal">Cetak</button>
                            @else
                                <a href="{{ route('product.edit', $i->id) }}" class="badge badge-danger">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{-- <div class="modal-header"> --}}
                {{-- <h5 class="modal-title" id="exampleModalLabel">Masukkan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                {{-- {{ $selectedProduct }} --}}
                {{-- </div> --}}
                <div class="modal-body">
                    <label for="">Masukkan Jumlah yang ingin di cetak</label>
                    <input type="number" class="form-control" wire:model="jumlah">
                    @error('jumlah')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="cetak()">Cetak</button>
                </div>
            </div>
        </div>
    </div>

</div>
