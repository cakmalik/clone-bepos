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
                        Penerimaan Pembelian
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="pt-3 pb-2 px-3">
                        <div class="row">
                            <div class="col-md-4">
                                <h5 class="text-muted">Tanggal</h5>
                                <h3 id="timePN" class="font-weight-bold text-dark"></h3>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Penerima</h5>
                                <select name="recipient_id" id="recipient_id" class="form-select">
                                    <option value="" disabled selected class="text-muted">-- Pilih Penerima --</option>
                                    @foreach ($recipients as $recipient)
                                        <option 
                                            value="{{ $recipient->id }}" 
                                            data-type="{{ $recipient instanceof \App\Models\Inventory ? 'inventory' : 'outlet' }}">
                                            {{ $recipient instanceof \App\Models\Inventory ? 'Gudang' : 'Outlet' }} - {{ $recipient->name }}
                                        </option>
                                    @endforeach
                                </select>           
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-muted">Supplier</h5>
                                <h3 id="pn_supplier_name" class="font-weight-bold text-dark"></h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="row">
                            <form id="purchaseForm" action="{{ route('purchase_reception.store') }}" method="post" class="form-detect-unsaved">
                                @csrf

                                
                                
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                                    <div class="btn-list">
                                        @if (!session('simple_purchase'))
                                            @include('pages.purchase.purchase_reception.dataPurchaseOrder')
                                        @else
                                            @include('pages.purchase.purchase_reception.supplier')
                                            @include('pages.purchase.purchase_reception.products')
                                        @endif
                                
                                        @include('pages.purchase.purchase_reception.items')
                                    </div>
                                
                                    {{-- Faktur dan No. Tagihan --}}
                                    <div class="d-flex align-items-center ml-auto gap-3">
                                        <div class="form-group mb-0 mr-2" id="invoice_number_group" style="min-width: 250px; display: none;">
                                            <input type="text" class="form-control" name="invoice_number" placeholder="No. Tagihan Supplier" id="invoice_number">
                                            @error('invoice_number')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    
                                        <div class="form-check form-switch mb-0">
                                            <input type="hidden" name="is_invoiced" value="0">
                                            <input class="form-check-input" type="checkbox" id="is_invoiced"
                                                name="is_invoiced" value="1" {{ old('is_invoiced') ? 'checked' : '' }}
                                                onchange="toggleInvoiceInput(this)">
                                            <label class="form-check-label ml-1" for="is_invoiced">
                                                <i>Faktur</i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                </div>

                                <input type="hidden" name="user_id" value="{{ Auth()->user()->id }}">
                                <input type="hidden" name="inventory_id" id="inventory_id">
                                <input type="hidden" name="outlet_id" id="outlet_id">
                                <input type="hidden" name="purchase_date" id="pn_purchase_date">
                                <input type="hidden" name="supplier_id" id="pn_supplier_id">
                                <input type="hidden" name="po_id" id="pn_po_id">
                                <input type="hidden" name="po_code" id="pn_po_code">
                                <input type="hidden" id="po_id" >

                                <div class="row mt-4">
                                    <div class="col-md-2">
                                        <label class="form-label">Barcode</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Nama Produk</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga Jual</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Harga Beli (HPP)</label>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label">Qty</label>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Subtotal</label>
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label"></label>
                                    </div>
                                </div>
                                <div class="row" id="po">
                                </div>

                                <div class="row mt-3" id="nominal_discount" style="display: none;">
                                    <div class="col-md-11 d-flex justify-content-end align-items-center gap-3">
                                        <label for="pn_discount" class="mb-0">Diskon</label>
                                        <input type="text" class="form-control" style="width: 190px;" name="nominal_discount" id="pn_discount" value="0">
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-9">
                                        <h2 class="text-end">Total Tagihan</h2>
                                    </div>
                                    <div class="col-md-3">
                                        <h2 id="totalPN">Rp. 0</h2>
                                    </div>
                                </div>

                                @include('pages.purchase.purchase_reception.purchase_bonus')

                                <div class="mt-4 mb-3" style="text-align: right;">
                                    <button type="button" class="btn btn-light d-none" id="editButton" onclick="editForm()">
                                        <i class="fas fa-sync me-2"></i>
                                        Harga
                                    </button>
                                    <button type="submit" class="btn btn-primary d-none" id="btnPN">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan Penerimaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const recipientSelect = document.getElementById("recipient_id");

            recipientSelect.addEventListener("change", function () {
                const selectedOption = this.options[this.selectedIndex];
                const type = selectedOption.getAttribute("data-type");
                const id = selectedOption.value;

                // Kosongkan dulu
                document.getElementById("inventory_id").value = "";
                document.getElementById("outlet_id").value = "";

                if (type === "inventory") {
                    document.getElementById("inventory_id").value = id;
                } else if (type === "outlet") {
                    document.getElementById("outlet_id").value = id;
                }
            });
        });
    </script>

    <script>
        function toggleElementVisibilityById(id, show = true) {
            const el = document.getElementById(id);
            if (!el) return;
            
            if (show) {
                el.classList.remove('d-none');
                el.style.display = '';
            } else {
                el.classList.add('d-none');
                el.style.display = 'none';
            }
        }


        function editForm(productList) {
            $.ajax({
                url: "{{ url('/edit-price-multiple') }}",
                type: "PUT",
                data: {
                    productList: productList,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    Toast.fire({
                        icon: "success",
                        title: "Harga Berhasil Diubah",
                        showConfirmButton: false,
                        timer: 1500
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        window.setInterval(function() {
            $('#timePN').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);

        window.setInterval(function() {
            $('#pn_purchase_date').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);

        $(document).ready(function() {

            $('body').on('change', '#po_id', function() {
                let id = $(this).attr('data-id');
                let supplier_id = $(this).attr('data-supplier-id');
                let supplier_name = $(this).attr('data-supplier-name');
                let po_code = $(this).attr('data-code');


                $.ajax({
                    url: "{{ url('/getPO') }}",
                    type: "POST",
                    data: {
                        po_id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log("success", response);

                        $('#editButton').on('click', function () {
                            var productList = [];

                            $.each(response.response, function (key, value) {
                                var productId = value.id;
                                var buyPrice = $('#pn_price_' + productId).val().replace(/\./g, '');
                                var sellPrice = $('[name="' + productId + '_pn_product_price"]').val().replace(/\./g, '');
                                var qty = parseFloat($('[name="' + productId + '_pn_qty"]').val()) || 0;

                                // ✅ Validasi harga kosong atau bukan angka
                                if (!buyPrice || isNaN(buyPrice) || !sellPrice || isNaN(sellPrice)) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Input Tidak Valid',
                                        text: 'Harga tidak valid pada produk: ' + value.product_name,
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'OK'
                                    });
                                    return false; // Hentikan proses
                                }

                                // ✅ Validasi harga jual < harga beli
                                if (parseFloat(sellPrice) < parseFloat(buyPrice)) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Harga Tidak Sesuai',
                                        text: 'Harga jual tidak boleh lebih kecil dari harga beli pada produk: ' + value.product_name,
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'OK'
                                    });
                                    return false;
                                }

                                var newSubtotal = parseFloat(buyPrice) * qty;

                                productList.push({
                                    productId: productId,
                                    newPrice: buyPrice,
                                    newPriceProduct: sellPrice,
                                    newSubtotal: newSubtotal
                                });
                            });

                            if (productList.length > 0) {
                                editForm(productList);
                            }
                        });

                        $('#po').empty()
                        $('#pn_supplier_name').empty()
                        $('#pn_po_id').val(id);
                        $('#pn_po_code').val(po_code);
                        $('#pn_supplier_id').val(supplier_id);
                        $('#pn_supplier_name').append(supplier_name);
                        
                        $.each(response.response, function (key, value) {
                            // Append kolom Barcode
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control" name="${value.id}_pn_barcode" value="${value.barcode ? value.barcode : '-'}" readonly>
                                </div>
                            `);

                            // Append kolom Nama Produk
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control" name="${value.id}_pn_name" value="${value.product_name}" readonly>
                                </div>
                            `);

                            //Append kolom Harga Jual
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" 
                                        class="form-control price-input" 
                                        id="pn_product_price_${value.id}" 
                                        name="${value.id}_pn_product_price" 
                                        value="${new Intl.NumberFormat('id-ID').format(value.product_price)}">
                                </div>
                            `);

                            // Append kolom Harga Beli
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control price-input" name="${value.id}_pn_price" id="pn_price_${value.id}" value="${new Intl.NumberFormat('id-ID').format(value.price)}">
                                </div>
                            `);

                            // Append kolom Qty
                            $('#po').append(`
                                <div class="col-md-1 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control" name="${value.id}_pn_qty" value="${value.qty}" readonly>
                                </div>
                            `);

                            // Append kolom Subtotal
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control subtotal" id="pn_subtotal_${value.id}" name="${value.id}_pn_subtotal" value="${new Intl.NumberFormat('id-ID').format(value.subtotal)}" readonly>
                                </div>
                            `);

                            // (Opsional) kolom kosong untuk delete button atau spacing
                            $('#po').append(`
                                <div class="col-md-1 mt-3 po_row_${value.id}">
                                </div>
                            `);

                            // Hitung subtotal saat harga diedit
                            $('#pn_price_' + value.id).on('input', function () {
                                let updatedPrice = parseFloat($(this).val().replace(/\./g, "")) || 0;
                                let newSubtotal = updatedPrice * value.qty;

                                $('#pn_subtotal_' + value.id).val(new Intl.NumberFormat('id-ID').format(newSubtotal));
                                updateTotal();
                            });

                            updateTotal();
                        });
                    }
                });

                $(document).on('focus', '.price-input', function () {
                    this.select();
                });

                $('#reception-create').modal('hide');
                toggleElementVisibilityById('btnPN', true);
                toggleElementVisibilityById('editButton', true);
                toggleElementVisibilityById('btnBonusProduct', true);
            });
        })

        //supplier
        $(document).ready(function() {
            $('body').on('change', '#supplier_id', function() {

                let id = $(this).val();
                let supplier_id = $(this).attr('data-id');
                let supplier_name = $(this).attr('data-name');

                $.ajax({
                    url: "{{ url('/get_supplier') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#pn_supplier_name').empty();
                        $('#pn_supplier_id').val(response.supplier.id);
                        $('#pn_supplier_name').append(response.supplier.name);

                        if($.fn.DataTable.isDataTable('#tableko')) {
                            $('#tableko').DataTable().destroy();
                        }

                        console.log("Respons dari server: ", response);
                    
                    }
                })

                $('#purchase-reception-supplier-create').modal('hide');
                toggleElementVisibilityById('btnSupplier', true);
                toggleElementVisibilityById('btnAddProduct', true);
            })
        })
            
        $(document).ready(function () {
            let productItems = [];

            $('body').on('click', '#items_product', function () {
                let id = $(this).data('id');
                productItems.push(id);

                // Ubah tombol + jadi badge checklist
                $(this).replaceWith(
                    "<span id='check_product_" + id + "' class='btn btn-primary py-2 px-3 product-status' data-id='" + id + "'><i class='fas fa-check text-white'></i></span>"
                );

                $.ajax({
                    url: "{{ url('/get_product_receiption') }}",
                    type: "POST",
                    data: {
                        items: [id],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $.each(response.response, function (key, value) {
                            // Barcode
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control" name="${value.id}_pn_barcode" value="${value.barcode ? value.barcode : '-'}" readonly>
                                </div>
                            `);

                            // Nama Produk
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" class="form-control" name="${value.id}_pn_name" value="${value.name}" readonly>
                                </div>
                            `);

                            // Harga Jual (product_price)
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" 
                                        class="form-control price-input" 
                                        id="pn_product_price_${value.id}" 
                                        name="${value.id}_pn_product_price" 
                                        value="${value.product_price[0].price}" readonly>
                                </div>
                            `);

                            // Harga Modal (capital_price)
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" 
                                        class="form-control price-input" 
                                        id="pn_capital_price_${value.id}" 
                                        name="${value.id}_pn_capital_price" 
                                        value="${value.capital_price}" readonly>
                                </div>
                            `);

                            // Qty input
                            $('#po').append(`
                                <div class="col-md-1 mt-3 po_row_${value.id}">
                                    <input type="number" 
                                        class="form-control qty-input" 
                                        id="pn_qty_${value.id}" 
                                        name="${value.id}_pn_qty" 
                                        value="1" 
                                        min="1">
                                </div>
                            `);

                            // Subtotal awal = harga modal (capital_price) * qty
                            const initialSubtotal = value.capital_price * 1;
                            $('#po').append(`
                                <div class="col-md-2 mt-3 po_row_${value.id}">
                                    <input type="text" 
                                        class="form-control subtotal" 
                                        id="pn_subtotal_${value.id}" 
                                        name="${value.id}_pn_subtotal" 
                                        value="${initialSubtotal}" 
                                        readonly>
                                </div>
                            `);

                            // Tombol hapus
                            $('#po').append(`
                                <div class="col-md-1 mt-3 po_row_${value.id}">
                                    <button type="button" class="btn btn-danger remove-product" data-id="${value.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `);

                            updateTotal();
                        });
                    }
                });

                $(document).on('focus', '.qty-input', function () {
                    this.select();
                });

                $('#reception-create').modal('hide');
                toggleElementVisibilityById('btnPN', true);
                toggleElementVisibilityById('btnBonusProduct', true);
            });

            // Hapus produk
            $('body').on('click', '.remove-product', function () {
                let id = $(this).data('id');

                // Hapus baris produk
                $('.po_row_' + id).remove();

                // Hapus dari list selected
                productItems = productItems.filter(item => item !== id);

                // Ganti badge checklist kembali ke tombol +
                let badge = $('#check_product_' + id);
                badge.replaceWith(
                    '<a href="javascript:void(0)" id="items_product" class="btn btn-outline-primary py-2 px-3" data-id="' + id + '">' +
                    '<i class="fas fa-plus"></i></a>'
                );

                // Sembunyikan tombol simpan kalau kosong
                if (productItems.length === 0) {
                    $('#btnPN').hide();
                }

                updateTotal();
            });
            

            // Update subtotal saat qty atau capital_price berubah
            $(document).on('input', '.qty-input, .price-input', function () {
                let id = $(this).attr('id').split('_')[2];
                let capitalPrice = parseFloat($('#pn_capital_price_' + id).val().replace(/\./g, '').replace(',', '.')) || 0;
                let qty = parseInt($('#pn_qty_' + id).val()) || 0;
                let subtotal = capitalPrice * qty;

                $('#pn_subtotal_' + id).val(subtotal);
                updateTotal();
            });

        });

        $(document).ready(function () {
            let bonusItems = [];

            // Ketika item bonus dipilih
            $('body').on('click', '#items_bonus', function () {
                let id = $(this).data('id');
                bonusItems.push(id);

                // Ganti tombol + jadi badge checklist
                $(this).replaceWith(
                    "<span id='check_bonus_" + id + "' class='btn btn-primary py-2 px-3 bonus-status' data-id='" + id + "'><i class='fas fa-check text-white'></i></span>"
                );

                toggleElementVisibilityById('bonus', true);
                toggleElementVisibilityById('bonus_items', true);

                $.ajax({
                    url: "{{ url('/get_product_bonus') }}",
                    type: "POST",
                    data: {
                        items: [id],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        $.each(response.response, function (key, value) {
                            let rowClass = 'bonus_product_' + value.id;

                            let row = `
                                <div class="row ${rowClass}">
                                    <input type="hidden" name="${value.id}_bonus_product_code" value="${value.code}">
                                    
                                    <div class="col-md-2 mt-3">
                                        <input type="text" class="form-control" name="${value.id}_bonus_barcode" value="${value.barcode ? value.barcode : '-'}" readonly>
                                    </div>

                                    <div class="col-md-5 mt-3">
                                        <input type="text" class="form-control" name="${value.id}_bonus_product_name" value="${value.name}" readonly>
                                    </div>

                                    <div class="col-md-2 mt-3">
                                        <input type="text" class="form-control" id="bonus_product_price_${value.id}" name="${value.id}_bonus_product_price" value="${(value.product_price && value.product_price.length > 0 ? value.product_price[0].price : '0')}" readonly>
                                    </div>

                                    <div class="col-md-1 mt-3">
                                        <input type="number" class="form-control qty-input" id="bonus_qty_${value.id}" name="${value.id}_bonus_qty" value="1" min="0">
                                    </div>

                                    <div class="col-md-1 mt-3">
                                        <button type="button" class="btn btn-danger remove-bonus" data-id="${value.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;

                            $('#bonus').append(row);

                            $('#bonus_qty_' + value.id).on('input', function () {
                                let qty = parseFloat($(this).val()) || 0;
                                let price = parseFloat($('#bonus_product_price_' + value.id).val()) || 0;
                                let subtotal = qty * price;
                                $('#bonus_subtotal_' + value.id).val(subtotal);
                            });
                        });
                    }
                });

                $(document).on('focus', '.qty-input', function () {
                    this.select();
                });
            });

            // Ketika bonus dihapus
            $(document).on('click', '.remove-bonus', function () {
                let id = $(this).data('id');

                // Hapus elemen bonus
                $('.bonus_product_' + id).remove();

                // Ganti badge checklist kembali ke tombol +
                let badge = $('#check_bonus_' + id);
                badge.replaceWith(
                    '<a href="javascript:void(0)" id="items_bonus" class="btn btn-outline-primary py-2 px-3" data-id="' + id + '">' +
                    '<i class="fas fa-plus"></i></a>'
                );

                // Hapus dari array bonus
                bonusItems = bonusItems.filter(item => item !== id);
            });

            // Submit form bonus
            $('#btnPN').submit(function (event) {
                event.preventDefault();
                $(this).append('<input type="hidden" name="bonus_products" value="' + JSON.stringify(bonusItems) + '">');
                this.submit();
            });
        });


        // Fungsi untuk memformat angka menjadi format grup ribuan
        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');
            return group;
        }

        // Fungsi untuk mengubah format Rupiah string ke angka
        function parseRupiahToNumber(str) {
            return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
        }

        // Fungsi untuk menghitung dan menampilkan total
        function updateTotal() {
            let totalPN = 0;

            // Hitung subtotal
            $('.subtotal').each(function () {
                let subtotalValue = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0;
                totalPN += subtotalValue;
            });

            let discount = 0;

            // Jika toggle faktur aktif, ambil nilai diskon
            if (document.getElementById('is_invoiced').checked) {
                discount = parseRupiahToNumber(document.getElementById('pn_discount').value);

                // Validasi agar diskon tidak melebihi total
                if (discount > totalPN) {
                    discount = totalPN;
                    $('#pn_discount').val(groupNumber(discount)); // perbarui tampilan input
                }
            }

            let finalTotal = totalPN - discount;
            finalTotal = finalTotal < 0 ? 0 : finalTotal;

            $('#totalPN').text('Rp. ' + groupNumber(finalTotal));
        }

        // Format dan validasi input diskon
        $('#pn_discount').on('input', function () {
            let inputVal = $(this).val().replace(/\D/g, ''); // hanya angka
            $(this).val(inputVal.replace(/\B(?=(\d{3})+(?!\d))/g, '.')); // format ribuan

            updateTotal();
        });

        // Auto-select saat fokus
        document.getElementById('pn_discount').addEventListener('focus', function () {
            this.select();
        });
        

        // Fungsi toggle faktur
        function toggleInvoiceInput(checkbox) {
            const invoiceInputGroup = document.getElementById('invoice_number_group');
            const nominalDiscount = document.getElementById('nominal_discount');

            const isChecked = checkbox.checked;

            invoiceInputGroup.style.display = isChecked ? 'block' : 'none';
            nominalDiscount.style.display = isChecked ? 'block' : 'none';

            if (!isChecked) {
                document.getElementById('pn_discount').value = 0;
                document.getElementById('invoice_number').value = '';
            }

            updateTotal(); // update total saat toggle berubah
        }

        // Jalankan saat DOM siap
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('is_invoiced');
            toggleInvoiceInput(toggle); // inisialisasi status awal
        });
    </script>

    <script>
        document.querySelectorAll('.po-button').forEach(button => {
            button.addEventListener('click', () => {
                let selectedSalesId = button.getAttribute('data-id');
                let supplierId = button.getAttribute('data-supplier-id');
                let supplierName = button.getAttribute('data-supplier-name');
                let poCode = button.getAttribute('data-code');

                // Reset semua tombol
                document.querySelectorAll('.po-button').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                    btn.querySelector('.button-text').textContent = 'Pilih';
                    btn.querySelector('.icon-check').style.display = 'none';
                });

                // Aktifkan tombol yang dipilih
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-primary');
                button.querySelector('.button-text').textContent = 'Dipilih';
                button.querySelector('.icon-check').style.display = 'inline-block';

                $('#po_id')
                    .val('')
                    .trigger('change')
                    .val(selectedSalesId)
                    .attr('data-id', selectedSalesId)
                    .attr('data-supplier-id', supplierId)
                    .attr('data-supplier-name', supplierName)
                    .attr('data-code', poCode)
                    .trigger('change');
            });
        });
    </script>

    <script>
        document.querySelectorAll('.supplier-button').forEach(button => {
            button.addEventListener('click', () => {
                let selectedSupplierId = button.getAttribute('data-id');
                let selectedSupplierName = button.getAttribute('data-name');

                // Update UI
                document.querySelectorAll('.supplier-button').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                    btn.querySelector('.button-text').textContent = 'Pilih';
                    btn.querySelector('.icon-check').style.display = 'none';
                });

                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-primary');
                button.querySelector('.button-text').textContent = 'Dipilih';
                button.querySelector('.icon-check').style.display = 'inline-block';

                $.ajax({
                    url: "{{ url('/get_supplier') }}",
                    type: "POST",
                    data: {
                        id: selectedSupplierId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#pn_supplier_name').text(response.supplier.name);
                        $('#pn_supplier_id').val(response.supplier.id);

                        if ($.fn.DataTable.isDataTable('#tableko')) {
                            $('#tableko').DataTable().destroy();
                        }

                        console.log("Respons dari server: ", response);
                    }
                });

                $('#purchase-reception-supplier-create').modal('hide');
                toggleElementVisibilityById('btnSupplier', true);
                toggleElementVisibilityById('btnAddProduct', true);
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#recipient_id').val('').trigger('change');
        });
    </script>


@endpush
