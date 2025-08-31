@extends('layouts.app')

@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Detail Product</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-auto ms-auto d-print-none gap-2">
                    <div class="btn-list">
                        <a href="{{ route('product.index') }}" class="btn btn-outline-primary">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="btn-list">
                        <a href="{{ route('product.edit', $products->id) }}" class="btn btn-primary py-2 px-3">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card mb-5 p-2">
                    <div class="card-header d-none d-md-flex justify-content-between">
                        <h3 class="card-title">{{ $products->name }}</h3>
                        @if ($products->barcode)
                            <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG(strval($products->barcode), 'C128') !!}" alt="barcode" />
                        @endif
                    </div>
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label>Kode Produk</label>
                                <h4><span class="badge badge-sm bg-green-lt">{{ $products->code }}</span></h4>
                            </div>
                            <div class="col-md-3">
                                <label>Kategori Produk</label>
                                <h4>{{ $products->productCategory->name }}</h4>
                            </div>
                            <div class="col-md-3">
                                <label>Satuan Produk</label>
                                <h4>{{ $products->productUnit->name }}</h4>
                            </div>
                            <div class="col-md-3">
                                <label>Tipe Produk</label>
                                <h4>{{ $products->type_product }}</h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label>Minimal Stok</label>
                                <h4>{{ $products->minimum_stock }}</h4>
                            </div>
                            <div class="col-md-3">
                                <label>HPP</label>
                                <h4>Rp {{ number_format($products->capital_price, 2, ',', '.') }}</h4>
                            </div>
                            <div class="col-md-3">
                                <label>Dibuat Tanggal</label>
                                <h4>{{ date('d F Y H:i', strtotime($products->created_at)) }}</h4>
                            </div>
                            <div class="col-md-3">
                                <label>Terakhir Diperbarui</label>
                                <h4>{{ date('d F Y H:i', strtotime($products->updated_at)) }}</h4>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label>Supplier</label>
                                <h4>{{ $supplierName ?? '-' }}</h4>
                            </div>

                            <div class="col-md-3">
                                <label>Deskripsi</label>
                                <h4>{{ $products->desc }}</h4>
                            </div>
                        </div>

                        <hr />

                        <!-- Tabel Harga Utama -->
                        <div class="row">
                            <div class="col-12">
                                <h4>Harga Utama</h4>
                                <table id="productPriceTable" class="table table-bordered">
                                    <thead class="font-weight-bold">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="30%">Penjualan</th>
                                            <th width="50%">Harga</th>
                                            <th>Jenis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($productPrice as $key => $value)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $value->price_name }}</td>
                                                <td>Rp {{ number_format($value->price, 2, ',', '.') }}
                                                    /{{ $value->unit_symbol }}</td>
                                                <td>
                                                    <span
                                                        class="badge badge-sm {{ $value->type == 'utama' ? 'bg-green-lt' : 'bg-orange-lt' }}">
                                                        {{ $value->type }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Harga Penjualan Belum Ditambahkan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Tabel Harga Berdasarkan Kategori Pelanggan -->
                        @if (config('app.current_version_product') == 'retail_advance' && $is_customer_price)
                            <div class="row mt-5">
                                <div class="col-12">
                                    <h4>Harga Berdasarkan Kategori Pelanggan</h4>
                                    <table id="customerPriceTable" class="table table-bordered">
                                        <thead class="font-weight-bold">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="30%">Kategori Pelanggan</th>
                                                <th width="50%">Harga</th>
                                                <th>Jenis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($customerPrices as $key => $customerPrice)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $customerPrice->category_name }}</td>
                                                    <td>Rp {{ number_format($customerPrice->price, 2, ',', '.') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-sm {{ $customerPrice->type == 'utama' ? 'bg-green-lt' : 'bg-orange-lt' }}">
                                                            {{ $customerPrice->type }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Harga Berdasarkan Kategori
                                                        Pelanggan Belum Ditambahkan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif


                        @if (config('app.current_version_product') == 'retail_advance' && $products->is_bundle)
                            <div class="row my-5">
                                <div class="col-12">
                                    <h4>Daftar Produk Dalam Paket</h4>

                                    @if ($products->is_main_stock)
                                        *<small><i>Penjualan dilakukan dari stok produk utama</i></small>
                                    @else
                                        *<small><i>Penjualan tidak mengurangi stok produk utama</i></small>
                                    @endif

                                    <table id="productBundleTable" class="table table-bordered">
                                        <thead class="font-weight-bold">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="60%">Nama Produk</th>
                                                <th width="20%">QTY</th>
                                                <th>Satuan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($productBundles as $key => $productBundle)
                                                <tr>
                                                    <td>{{ ++$key }}</td>
                                                    <td>{{ $productBundle->mainProduct->name }}</td>
                                                    <td>{{ $productBundle->qty }}</td>
                                                    <td>{{ $productBundle->mainProduct->productUnit->name }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Produk Dalam Paket Belum
                                                        Ditambahkan
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>

                                    </table>

                                </div>
                            </div>
                        @endif

                        {{-- <hr /> --}}

                        <!-- Gambar Produk -->
                        {{-- <div class="row mt-3">
                            <div class="col-12">
                                <label>Gambar</label>
                                <div class="mt-3">
                                    <img src="{{ asset('storage/images/' . $products->image) }}" class="img-fluid"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default_img.jpg') }}';">
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            if ($.fn.dataTable.isDataTable('#productPriceTable')) {
                $('#productPriceTable').DataTable().destroy();
            }
            $('#productPriceTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                lengthChange: false
            });

            if ($.fn.dataTable.isDataTable('#customerPriceTable')) {
                $('#customerPriceTable').DataTable().destroy();
            }
            $('#customerPriceTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                lengthChange: false
            });

            if ($.fn.dataTable.isDataTable('#productBundleTable')) {
                $('#productBundleTable').DataTable().destroy();
            }
            $('#productBundleTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                lengthChange: false
            });
        });
    </script>
@endpush
