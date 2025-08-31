@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Update Product
                    </h2>
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
                    <div class="card">
                        <div class="card-header d-none d-md-flex justify-content-between">
                            <h3 class="card-title">Update Product</h3>
                            {{-- @if ($products->barcode)
                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($products->barcode, 'EAN13') }}"
                                    alt="barcode" />
                            @endif --}}
                        </div>
                        <form action="{{ route('updatePricePurchase.update', $products->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="row">
                                            <div class="col">
                                                <label class="form-label">Code *</label>
                                                <input type="text" autocomplete="off" value="{{ $products->code }}"
                                                    readonly placeholder="Auto-Generate"
                                                    class="form-control @error('code') is-invalid @enderror" id="code"
                                                    data-initial-value="{{ $products->code }}">
                                                @error('code')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label class="form-label">Barcode</label>
                                                <input type="text" autocomplete="off" name="barcode"
                                                    value="{{ $products->barcode }}" placeholder=""
                                                    class="form-control @error('barcode') is-invalid @enderror"
                                                    id="barcode" data-initial-value="{{ $products->barcode }}">
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
                                            value="{{ old('name') ?? $products->name }}">
                                        @error('name')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">Tipe *</label>
                                        <select id="type_product" name="type_product"
                                            class="form-control  @error('type_product') is-invalid @enderror"
                                            data-initial-value="{{ $products->type_product }}">
                                            @foreach ($typeProducts as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ (old('type_product') ?? $products->type_product) == $key ? 'selected' : '' }}>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                        @error('type_product')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror

                                        <div class="col-12 col-md-6 mb-3 mt-3">
                                            <label class="form-label">Minimum Stock & unit *</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="minimum_stock"
                                                    aria-label="Minimum Stocks"
                                                    value="{{ old('minimum_stock') ?? $products->minimum_stock }}">
                                                <select name="product_unit"
                                                    class="form-control  @error('product_unit') is-invalid @enderror">
                                                    <option value="0">Pilih Satuan</option>
                                                    @foreach ($productsUnit as $pu)
                                                        <option value="{{ $pu->id }}"
                                                            {{ (old('product_unit') ?? $products->product_unit_id) == $pu->id ? 'selected' : '' }}>
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
                                            class="form-control  @error('product_category') is-invalid @enderror">
                                            @if ($products->productCategory?->is_parent_category == false)
                                                @foreach ($productsCategory as $cp)
                                                    <option value="{{ $cp->id }}"
                                                        @if ($products->productCategory?->parent?->id === $cp->id) selected @endif>
                                                        {{ $cp->name }}</option>
                                                @endforeach
                                            @else
                                                {{-- <option value="">Pilih</option> --}}
                                                @foreach ($productsCategory as $cp)
                                                    <option value="{{ $cp->id }}"
                                                        @if ($products->productCategory?->id === $cp->id) selected @endif>
                                                        {{ $cp->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('product_category')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror

                                        <label class="form-label mt-3">Sub Kategori (opsional) </label>

                                        <select name="sub_category_id" class="form-select">
                                            @if ($products->productCategory?->is_parent_category == false)
                                                {{-- <option value="{{ $products->productCategory?->id }}">
                                                    {{ $products->productCategory->name }}</option> --}}
                                                @foreach ($sub_categories as $sc)
                                                    <option value="{{ $sc->id }}"
                                                        {{ $products->productCategory?->id === $sc->id ? 'selected' : '' }}>
                                                        {{ $sc->name }}</option>
                                                @endforeach
                                            @else
                                                <option value="">Pilih</option>
                                                @foreach ($sub_categories as $sc)
                                                    <option value="{{ $sc->id }}"
                                                        {{ $products->productCategory?->id === $sc->id ? 'selected' : '' }}>
                                                        {{ $sc->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="ccol-12 col-md-6 mb-3">
                                        <label class="form-label">Nama Supplier *</label>
                                        <select name="supplier"
                                            class="form-control  @error('supplier') is-invalid @enderror" required>
                                            <option value="" disabled selected> &mdash; Pilih Supplier &mdash;
                                            </option>
                                            @foreach ($suppliers as $s)
                                                @if ($supplier && $supplier->supplier_id == $s->id)
                                                    <option value="{{ $s->id }}" selected>{{ $s->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('supplier')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">Nama Brand (Opsional)</label>
                                        <select name="brand" class="form-control  @error('brand') is-invalid @enderror">
                                            <option value="" disabled selected> &mdash; Pilih Brand &mdash;
                                            </option>
                                            @foreach ($brand as $s)
                                                @if ($products->brand_id == $s->id)
                                                    <option value="{{ $s->id }}" selected> {{ $s->name }}
                                                    </option>
                                                @else
                                                    <option value="{{ $s->id }}"> {{ $s->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('brand')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row">

                                </div>

                                <hr />

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">Capital Price *</label>
                                        <input type="hidden" name="capital_price" id="capital_price"
                                            value="{{ old('capital_price') ?? $products->capital_price }}">
                                        <input type="text" data-currency-value="#capital_price" autocomplete="off"
                                            class="form-control mb-3 currency @error('capital_price') is-invalid @enderror">
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
                                                @foreach ($productPrices as $key => $sp)
                                                    @php
                                                        $oldInputPrice = is_null(old('product_prices')) ? [] : old('product_prices')[$key];
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            {{ $sp->name }}
                                                        </td>
                                                        <td class="p-0">
                                                            <input type="hidden"
                                                                name="product_prices[{{ $key }}][selling_price_id]"
                                                                value="{{ $sp->id }}">
                                                            <input type="hidden"
                                                                id="price_{{ Str::lower($sp->name) }}_id"
                                                                name="product_prices[{{ $key }}][price]"
                                                                value="{{ $oldInputPrice['price'] ?? $sp->price }}">
                                                            <input type="text" autocomplete="off"
                                                                data-currency-value="#price_{{ Str::lower($sp->name) }}_id"
                                                                class="currency form-control @error('price') is-invalid @enderror"
                                                                style="border: none;" placeholder="Rp 0" value="">
                                                        </td>
                                                        <td>
                                                            <input type="radio" name="main_selling_price_id"
                                                                value="{{ $sp->id }}"
                                                                {{ old('main_selling_price_id') ? (old('main_selling_price_id') == $sp->id ? 'checked' : '') : ($sp->type == 'utama' ? 'checked' : '') }}>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @error('main_selling_price_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="form-label">Gambar</label>
                                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                                            name="image" accept="image/*" value="{{ old('image') }}"
                                            onchange="loadFile(event)">
                                        @error('image')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror

                                        <div class="mt-3 d-flex justify-content-center">
                                            <img id="output" src="{{ asset('storage/images/' . $products->image) }}"
                                                style="object-fit: cover; width: 300px; height: 300px;"
                                                onerror="this.onerror=null;this.src='{{ asset('img/default_img.jpg') }}';" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="reset" class="btn btn-link">Reset</button>
                                    <button type="submit" class="btn btn-success ms-auto"><i
                                            class="fa-solid fa-floppy-disk"></i> &nbsp; Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.currency').each(function() {
                const value_element = $($(this).data('currency-value'))
                const data_real = value_element.val() || 0
                $(this).val(generateCurrency(Number(data_real)))
                    .on('input', function() {
                        const number = parseCurrency(this.value)
                        const text = generateCurrency(number)
                        value_element.val(number)
                        $(this).val(text)
                    })
            })

            $('#type_product').on('change', function() {
                const type = $(this).val()
                const initValue = $(this).data('initial-value')
                const code = $('#code')
                if (type == initValue) {
                    code.val(code.data('initial-value'))
                } else {
                    code.val(null)
                }
            })
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


                // console.log('categoryId', categoryId);
                // Hapus opsi subkategori yang ada sebelumnya
                $('select[name="sub_category_id"]').empty();

                // Mengambil token CSRF dari meta tag
                var token = $('meta[name="csrf-token"]').attr('content');

                // Kirim permintaan Ajax untuk mendapatkan subkategori berdasarkan kategori yang dipilih
                $.ajax({
                    url: '/getSubCategories',
                    type: 'POST',
                    data: {
                        category_id: categoryId,
                        _token: token
                    },
                    success: function(response) {
                        console.log(response);
                        // Tambahkan opsi subkategori ke dalam select
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
    </script>
@endpush
