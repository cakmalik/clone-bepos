@extends('layouts.app')
@section('page')

<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title mb-0">Informasi Stok Habis</h2>
                    <small class="text-muted d-block" id="salesStatistic"></small>
                </div>
                
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="card py-3" style="border-radius: 1rem">
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="outOfStockTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Barcode</th>
                                    <th>Nama Produk</th>
                                    <th class="text-end">Kategori</th>
                                    <th class="text-end">Minimal Stok</th>
                                    <th class="text-end">Sisa stok</th>
                                    <th class="text-center">Outlet</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stockMinimum as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->barcode }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category_name }}</td>
                                        <td class="text-end">{{ floatval($item->minimum_stock) }} {{ $item->unit_name }}</td>
                                        <td class="text-end">{{ floatval($item->stock_current) }} {{ $item->unit_name }}</td>
                                        <td>{{ $item->outlet_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $('#outOfStockTable').DataTable({
                pageLength: 25,
        });
    </script>
@endpush