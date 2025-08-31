<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-none d-md-flex justify-content-between">
                        <h3 class="card-title">{{ $products->name }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('product.store-or-update-item-bundle', $products->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_main_stock" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_main_stock"
                                        name="is_main_stock" value="1"
                                        {{ old('is_main_stock', $products->is_main_stock) ? 'checked' : '' }}>
                                    <small><i>Aktifkan untuk penjualan dari stok produk utama</i></small>
                                </div>

                                <div class="btn-list mt-4">
                                    @include('pages.product.product_all.items')
                                </div>

                                <div id="product-container" class="mt-4">
                                    <div class="row mb-3">
                                        <div class="col-md-3"><strong>Nama Produk</strong></div>
                                        <div class="col-md-2"><strong>QTY</strong></div>
                                        <div class="col-md-3"><strong>Satuan</strong></div>
                                        <div class="col-md-1"></div>
                                    </div>

                                    <div class="row" id="items">
                                        @foreach ($itemBundles as $bundle)
                                            <div class="row item_bundle_{{ $bundle->mainProduct->id }}">
                                                <input type="hidden" name="items[]"
                                                    value="{{ $bundle->mainProduct->id }}">

                                                <div class="col-md-3 mt-3">
                                                    <input type="text" class="form-control"
                                                        name="{{ $bundle->mainProduct->id }}_name"
                                                        value="{{ $bundle->mainProduct->name }}" readonly>
                                                </div>

                                                <div class="col-md-2 mt-3">
                                                    <input type="number" min="1" class="form-control qty-input"
                                                        name="{{ $bundle->mainProduct->id }}_qty"
                                                        value="{{ $bundle->qty }}" required>
                                                </div>

                                                <div class="col-md-3 mt-3">
                                                    <input type="text" class="form-control"
                                                        name="{{ $bundle->mainProduct->id }}_unit"
                                                        value="{{ $bundle->mainProduct->productUnit->name ?? '' }}"
                                                        readonly>
                                                </div>

                                                <div class="col-md-1 mt-3">
                                                    <button type="button" class="btn btn-danger remove-product"
                                                        data-id="{{ $bundle->mainProduct->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ms-auto">
                                        <i class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#is_main_stock').on('change', function() {
                    let isChecked = $(this).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: "{{ route('product.toggle-main-stock', $products->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            is_main_stock: isChecked
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menyimpan perubahan',
                            });
                        }
                    });
                });
            });
        </script>
        <script>
            $(function() {

                $(document).on("keyup", '#dataTable_filter input', function() {
                    table.draw();
                });

                var table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 25,
                    destroy: true,
                    order: false,
                    ajax: {
                        url: '{{ url()->current() }}',
                        data: function(d) {
                            d.product = $('#dataTable_filter input').val();
                            console.log('Product:', d);
                        }
                    },
                    columns: [{
                            data: 'barcode',
                            name: 'barcode'
                        },
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data, type, row) {
                                var words = data.split(' ').slice(0, 5).join(' ');
                                var ellipsis = data.split(' ').length > 5 ? '...' : '';
                                return '<span class="truncated-text" title="' + data + '">' + words +
                                    ellipsis + '</span>';
                            }
                        },
                        {
                            data: 'product_category',
                            name: 'product_category'
                        },
                        {
                            data: 'unit_name',
                            name: 'unit_name'
                        },
                        {
                            data: 'capital_price',
                            name: 'capital_price'
                        },
                        {
                            "className": "dt-center",
                            data: 'action',
                            name: 'action'
                        },
                    ]
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                Array.prototype.remove = function() {
                    var what, a = arguments,
                        L = a.length,
                        ax;
                    while (L && this.length) {
                        what = a[--L];
                        while ((ax = this.indexOf(what)) !== -1) {
                            this.splice(ax, 1);
                        }
                    }
                    return this;
                };
                $('#btnPR').hide();

                $('#history_button').hide();
                let items = [];
                $('body').on('click', '#items_product', function() {
                    let id = $(this).data('id');
                    items.push(id);
                    $(this).hide()
                    $(this).replaceWith(
                        "<span class='badge badge-sm bg-green'><i class='fas fa-check text-white'></i></span>"
                    );
                    $.ajax({
                        url: "{{ url('/getProductBundle') }}",
                        type: "POST",
                        data: {
                            items: [id],
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            console.log(response);

                            $.each(response.response, function(key, value) {
                                let row = `<div class="row item_bundle_${value.id}">`;

                                row +=
                                    `<input type="hidden" class="item_bundle_${value.id}" name="items[]" value="${value.id}">`;
                                row +=
                                    `<input type="hidden" class="item_bundle_${value.id}" name="${value.id}_id" value="${value.id}">`;

                                row += `
                                <div class="col-md-3 mt-3">
                                    <input type="text" class="form-control" name="${value.id}_name" 
                                        value="${value.name}" readonly>
                                </div>
                            `;

                                row += `
                                <div class="col-md-2 mt-3">
                                    <input type="number" min="0" class="form-control" name="${value.id}_qty" 
                                        id="${value.id}_qty" value="" required>
                                </div>
                            `;

                                row += `
                                <div class="col-md-3 mt-3">
                                    <input type="text" class="form-control" name="${value.id}_unit" 
                                        id="${value.id}_unit" value="${value.product_unit ? value.product_unit.name : ''}" readonly>
                                </div>
                            `;

                                row += `
                                <div class="col-md-1 mt-3">
                                    <button type="button" class="btn btn-danger removeProduct" 
                                        data-id="${value.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;

                                row += `</div>`; // **Tutup div.row**

                                $('#items').append(row);
                            });

                            // Hapus produk ketika tombol trash diklik
                            $('body').on('click', '.removeProduct', function() {
                                let id = $(this).data('id');
                                $('.item_bundle_' + id).remove();
                                items.remove(id);

                                let elemenTd = $('[data-id="pr_product_' + id + '"]')
                                    .closest('td');
                                if (elemenTd.length > 0) {
                                    let linkElement = `
                                    <a href="javascript:void(0)" id="items_product" class="btn btn-primary btn-sm py-2 px-3" data-id="${id}">
                                        <i class="fas fa-add"></i>
                                    </a>
                                `;
                                    elemenTd.html(linkElement);
                                }
                            });

                        }
                    });
                });
            })

            function toggleMainStock(checkbox) {
                if (!checkbox.checked) {
                    checkbox.value = '0';
                } else {
                    checkbox.value = '1';
                }
            }

            $(document).ready(function() {
                $('.remove-product').on('click', function() {
                    let id = $(this).data('id');
                    $('.item_bundle_' + id).remove();
                });
            });
        </script>
    @endpush
