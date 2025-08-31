@section('page-title')
    Laporan Nilai Stok
@endsection
@section('action-header')
@endsection
<div>
    <div class="card-header justify-content-between mb-3">
        <h3 class="card-title"><strong class="fw-bold">{{ $labelName }}</strong> <span
                class="text-uppercase fw-bold"> {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
        </h3>
        <div class="d-flex gap-3">
            @if (\Carbon\Carbon::parse($date)->isToday())
                <button class="btn btn-success d-flex align-items-center" wire:click='refreshData'>
                    <i class="ph ph-arrows-clockwise me-2"></i>
                    Refresh Data
                </button>
            @endif
            <button id="btnSubmitExport" class="btn btn-primary d-flex align-items-center">
                <i class="ph ph-export me-2"></i>
                Export
            </button>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-blue me-3" role="status" wire:loading wire:target='refreshData'></div><span wire:loading wire:target='refreshData'
            class="text-muted"> Memuat data ...</span>
    </div>

    <div class="table-responsive" wire:loading.remove wire:target='refreshData'>
        <form id="formX" action="{{ route('report.stock-value.export') }}" method="POST">
            @csrf
            <div class="row mb-4 align-items-end">
                <div class="col-md-2 mb-2">
                    <label for="out_id">Outlet</label>
                    <select name="out_id" id="out_id" class="form-select" wire:model='out_id'>
                        <option value="">-- Semua Outlet --</option>
                        @foreach ($outlets as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="inv_id">Gudang</label>
                    <select name="inv_id" id="inv_id" class="form-select" wire:model='inv_id'>
                        <option value="">-- Semua Gudang --</option>
                        @foreach ($inventories as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="date">Tanggal</label>
                    <input type="date" class="form-control" name='date' wire:model='date'
                        max="{{ date('Y-m-d') }}" id="date">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="product_category_id">Kategori</label>
                    <select name="product_category_id" id="product_category_id" class="form-select"
                        wire:model='product_category_id' name="product_category_id">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($product_categories as $key => $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <input type="text" class="form-control" placeholder="Cari Produk..." wire:model='searchTerm'
                        name="searchTerm">
                </div>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Nilai Stok</div>
                        </div>
                        <div class="h1 mb-3">Rp{{ number_format($total_stock_value) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Potensi Nilai</div>
                        </div>
                        <div class="h1 mb-3">Rp{{ number_format($total_potential_value) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Outlet</th>
                    <th>Gudang</th>
                    <th>Stok Awal</th>
                    <th>Pembelian</th>
                    <th>Penjualan</th>
                    <th>Stok Akhir</th>
                    <th>Harga Beli</th>
                    <th>Nilai Stok</th>
                    <th>Harga Jual</th>
                    <th>Potensi Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $index => $stock)
                    <tr>
                        <td>{{ $data->firstItem() + $index }}</td>
                        <td>{{ $stock->product->name ?? '-' }}</td>
                        <td>{{ $stock->productCategory->name ?? '-' }}</td>
                        <td>{{ $stock->outlet->name ?? '-' }}</td>
                        <td>{{ $stock->inventory->name ?? '-' }}</td>
                        <td>{{ $stock->initial_stock }}</td>
                        <td>{{ number_format($stock->purchases) }}</td>
                        <td>{{ $stock->sales }}</td>
                        <td>{{ $stock->final_stock }}</td>
                        <td>{{ number_format($stock->purchase_price) }}</td>
                        <td>{{ number_format($stock->stock_value) }}</td>
                        <td>{{ number_format($stock->selling_price) }}</td>
                        <td>{{ number_format($stock->potential_value) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center">Tidak ada data ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-center">{{ $data->links() }}</div>
    </div>

    <script>
        document.getElementById('btnSubmitExport').addEventListener("click", function() {
            document.getElementById("formX").submit();
        })
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(() => {
                Livewire.emit('initData')
            }, 500);
        });
        </script>

</div>
