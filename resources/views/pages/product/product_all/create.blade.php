@extends('layouts.app')
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Produk
                    </h2>
                </div>

                <div class="col-auto">
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm rounded-2">
                        <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    {{-- @if ($message = Session::get('error'))
                        <x-alert level="danger" message="{{ $message }}" />
                    @elseif($message = Session::get('success'))
                        <x-alert level="success" message="{{ $message }}" />
                    @endif
                    @foreach ($errors->all() as $error)
                        <x-alert level="danger" message="{{ $error }}" />
                    @endforeach --}}

                    @if ($can_add_product)
                        <div class="card pt-3">
                            <div class="card-body">
                                <form action="{{ route('product.create') }}" method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="form-label">Kode Produk</label>
                                                        <input name="code" type="text" autocomplete="off" readonly
                                                            placeholder="Auto-Generate"
                                                            class="form-control @error('code') is-invalid @enderror"
                                                            id="code">
                                                        @error('code')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col">
                                                        <label class="form-label">Barcode</label>
                                                        <input name="barcode" type="text" autocomplete="off"
                                                            name="barcode" placeholder="Scan / Input disini"
                                                            value="{{ old('barcode') }}"
                                                            class="form-control @error('barcode') is-invalid @enderror"
                                                            id="barcode">
                                                        @error('barcode')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Nama Produk *</label>
                                                <input name="name" type="text" autocomplete="off"
                                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                                    value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                                @if (config('app.current_version_product') == 'retail_advance')
                                                    <input type="hidden" name="is_bundle" value="0">
                                                    <div class="form-check form-switch pt-3">
                                                        <input class="form-check-input" type="checkbox" id="is_bundle"
                                                            name="is_bundle" value="0"
                                                            {{ old('is_bundle') ? 'checked' : '' }}
                                                            onchange="toggleBundleValue(this)">
                                                        <small><i>Aktikan produk sebagai penjualan paket</i></small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Tipe *</label>
                                                <select name="type_product"
                                                    class="form-control  @error('type_product') is-invalid @enderror">
                                                    @foreach ($typeProducts as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('type_product') == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('type_product')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                                <div class="col-12 col-md-6 mb-3 mt-3">
                                                    <label class="form-label">Stok Minimal & Satuan *</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="minimum_stock"
                                                            aria-label="Minimum Stocks" value="{{ old('minimum_stock') }}">
                                                        <select name="product_unit"
                                                            class="form-control  @error('product_unit') is-invalid @enderror">
                                                            <option value="">Pilih Satuan</option>
                                                            @foreach ($productsUnit as $pu)
                                                                <option value="{{ $pu->id }}"
                                                                    {{ old('product_unit') == $pu->id ? 'selected' : '' }}>
                                                                    {{ $pu->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error('product_unit')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Kategori *</label>
                                                <select name="product_category"
                                                    class="form-select  @error('product_category') is-invalid @enderror">
                                                    <option value="0">Pilih Kategori</option>
                                                    @foreach ($productsCategory as $cp)
                                                        <option value="{{ $cp->id }}"
                                                            {{ old('product_category') == $cp->id ? 'selected' : '' }}>
                                                            {{ $cp->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('product_category')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                                <label class="form-label mt-3">Sub Kategori (Opsional) </label>
                                                <select name="sub_category_id" class="form-select">
                                                </select>
                                                </select>
                                                @error('product_category')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Nama Supplier *</label>
                                                <select name="supplier"
                                                    class="form-select  @error('supplier') is-invalid @enderror">
                                                    <option value="" disabled selected> &mdash; Pilih Supplier
                                                        &mdash;
                                                    </option>
                                                    @foreach ($supplier as $s)
                                                        <option value="{{ $s->id }}"
                                                            {{ old('supplier') == $s->id ? 'selected' : '' }}>
                                                            {{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('supplier')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Nama Merk (Opsional)</label>
                                                <select name="brand"
                                                    class="form-select  @error('brand') is-invalid @enderror">
                                                    <option value="" disabled selected> &mdash; Pilih Merk &mdash;
                                                    </option>
                                                    @foreach ($brand as $s)
                                                        <option value="{{ $s->id }}"> {{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('brand')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>


                                        </div>

                                        <div class="row">

                                            <hr />

                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-3">
                                                    <label class="form-label">HPP *</label>
                                                    <input name="capital_price" id="capital_price"
                                                    class="form-control mb-4 currency text-end"
                                                    value="{{ old('capital_price', 0) }}"
                                                    onfocus="this.select()">                                                
                                                
                                                    @error('capital_price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror

                                                    <label class="form-label">Harga *</label>
                                                    <table id="prices-table" class="table table-bordered no-datatable">
                                                        <thead>
                                                            <tr>
                                                                <th>Tipe</th>
                                                                <th>Harga</th>
                                                                <th>Utama</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sellingPrice as $key => $sp)
                                                                @php
                                                                    $oldInputPrice = is_null(old('product_prices'))
                                                                        ? []
                                                                        : old('product_prices')[$key];
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        {{ $sp->name }}
                                                                    </td>
                                                                    <td class="p-0">
                                                                        <input type="hidden"
                                                                            name="product_prices[{{ $key }}][selling_price_id]"
                                                                            value="{{ $sp->id }}">

                                                                        <input type="text" autocomplete="off"
                                                                            name="product_prices[{{ $key }}][price]"
                                                                            value="{{ old('product_prices.' . $key . '.price', $oldInputPrice['price'] ?? 0) }}"
                                                                            id="price_{{ Str::lower($sp->name) }}_id"
                                                                            class="currency text-end form-control @error('product_prices.' . $key . '.price') is-invalid @enderror"
                                                                            style="border: none;" placeholder="Rp 0">
                                                                        
                                                                    </td>
                                                                    <td>
                                                                        <input type="radio" name="main_selling_price_id"
                                                                            value="{{ $sp->id }}"
                                                                            {{ old('main_selling_price_id') == $sp->id ? 'checked' : '' }}>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    @error('main_selling_price_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror

                                                </div>

                                                {{-- <div class="col-12 col-md-6 mb-3">
                                                <label class="form-label">Gambar</label>
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    name="image" accept="image/*" value="{{ old('image') }}"
                                                    onchange="loadFile(event)">
                                                @error('image')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror

                                                <div class="mt-3 d-flex justify-content-center">
                                                    <img id="output"
                                                        style="object-fit: cover; width: 300px; height: 300px;" />
                                                </div>
                                            </div> --}}
                                            </div>
                                        </div>
                                        <div class="card-footer text-end">
                                            <div class="d-flex">
                                                <button type="reset" class="btn btn-link">Muat ulang</button>
                                                <button type="submit" class="btn btn-primary ms-auto"><i
                                                        class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                                                </button>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <x-alert-to-upgrade />
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.form-select').select2()
        });

        var loadFile = function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('output');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
            $('#output').show();
        };
    </script>
    <script>
        $(document).ready(function() {
            $('select[name="product_category"]').change(function() {
                var categoryId = $(this).val();

                $('select[name="sub_category_id"]').empty();

                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/getSubCategories',
                    type: 'POST',
                    data: {
                        category_id: categoryId,
                        _token: token
                    },
                    success: function(response) {
                        console.log(response);
                        var subCategories = response.subCategories;
                        $('select[name="sub_category_id"]').append(
                            '<option value="">Pilih</option>');

                        $.each(subCategories, function(index, subCategory) {
                            $('select[name="sub_category_id"]').append(
                                '<option value="' + subCategory.id + '">' +
                                subCategory.name + '</option>');
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            });
        });

        function toggleBundleValue(checkbox) {
            if (!checkbox.checked) {
                checkbox.value = '0';
            } else {
                checkbox.value = '1';
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.currency');

            inputs.forEach(input => {
                // Saat load awal, format isian kalau belum diformat
                input.value = formatRupiah(input.value);

                input.addEventListener('input', function (e) {
                    let angka = this.value.replace(/[^0-9]/g, '');
                    this.value = formatRupiah(angka);
                });

                input.addEventListener('focus', function () {
                    this.select();
                });
            });

            function formatRupiah(angka) {
                if (!angka) return 'Rp. 0';

                angka = angka.toString();
                angka = angka.replace(/[^0-9]/g, '');

                let sisa = angka.length % 3;
                let rupiah = angka.substr(0, sisa);
                let ribuan = angka.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return 'Rp. ' + rupiah;
            }
        });
    </script>
@endpush
