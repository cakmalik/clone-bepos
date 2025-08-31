@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Edit Stok Produk
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <form method="POST" action="{{ route('productStock.updateAllStock') }}">
                    @csrf
                    @method('PUT')
                    <div class="card mb-5">
                        <div class="card-header">
                            <h3 class="card-title">Stok Produk</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    Halaman ini hanya menampilkan barang dengan tipe produk. Untuk mengganti stok bahan
                                    baku, gunakan halaman <a href="{{ route('stockOpname.index') }}">stock opname</a>.
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <label class="form-label">Kode Produk</label>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Nama Produk</label>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Stok</label>
                                        </div>
                                    </div>

                                    @foreach ($products as $key => $product)
                                        <div class="row mb-3">
                                            <div class="col-4">
                                                <input type="text" class="form-control" readonly
                                                    value="{{ $product->code }}">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control" readonly
                                                    value="{{ $product->name }}">
                                            </div>
                                            <div class="col-4">
                                                <input type="hidden" name="items[{{ $key }}][product_id]"
                                                    value="{{ $product->id }}">
                                                <input type="number" class="form-control"
                                                    name="items[{{ $key }}][qty]"
                                                    value="{{ isset($product->productStock[0]) ? $product->productStock[0]->stock_current : 0 }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
