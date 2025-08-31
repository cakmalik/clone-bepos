<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}

    <div class="form-group">
        <form action="{{ route('product.price-tag') }}" method="POST">
            @csrf
            <label for="cari" class="mb-2">Cari Nama/Kode
                produk</label>
            <div class="d-flex">
                <input wire:model.debounce.1000ms="cari" type="text" class="form-control" id="cari"
                    placeholder="Cari Nama/Kode produk">

                <input type="hidden" name="selectedProducts" value="{{ json_encode($selectedProducts) }}">
                <button type="submit" class="btn btn btn-primary ms-3"><i class="fa fa-tag me-2"
                        aria-hidden="true"></i>
                    Label Harga</button>
            </div>
        </form>
    </div>

    <div class="table-responsive my-3">
        <table
            class="no-datatable table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" wire:click="toggleAll" {{ $selectAll ? 'checked' : '' }}>
                    </th>
                    <th style="width: 5%">Nama Produk</th>
                    <th>Barcode</th>
                    <!--<th>Jumlah Produk</th>-->
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $i)
                    <tr>
                        <td>
                            <input type="checkbox" wire:click="toggleSelect({{ $i->id }})"
                                value="{{ $i->id }}" {{ in_array($i->id, $selectedProducts) ? 'checked' : '' }}>
                        </td>
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
                        <!--<td></td>-->
                        <td>
                            @if ($i->barcode)
                                <button class="btn btn btn-white" wire:click="selectedId({{ $i }})"
                                    data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa fa-print me-2"
                                        aria-hidden="true"></i>
                                    Barcode</button>
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
    <div class="d-flex justify-content-center text-center">
        {{ $products->links() }}
    </div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <label for="">Masukkan Jumlah yang ingin di cetak</label>
                    <input type="number" class="form-control" wire:model="jumlah">
                    @error('jumlah')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="modal-footer">
                    <a class="text-decoration-none px-3" data-bs-dismiss="modal">Tutup</a>
                    <button type="button" class="btn btn-primary" wire:click="cetak()">Cetak</button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
@endpush
