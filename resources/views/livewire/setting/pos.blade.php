{{-- Close your eyes. Count to one. That is how long forever feels. --}}
<div class="">
    <div class="row row-cards">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header h3">
                Operasional POS
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3 d-flex align-items-center">
                            <h3>Kasir</h3>
                        </div>

                        {{-- Stok Minus --}}
                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch id="stockMinusToggle" :is_checked="$stock_minus" wire:model="stock_minus" />
                            <div class="ms-2">
                                <div class="fw-semibold">Penjualan Stok Minus</div>
                                <small class="text-muted">Peraturan ini akan mengizinkan penjualan tanpa stok</small>
                            </div>
                        </div>

                        {{-- Validasi Atasan --}}
                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$superior_validation" wire:model="superior_validation" />
                            <div class="ms-2">
                                <div class="fw-semibold">Validasi Atasan untuk Hapus Item</div>
                                <small class="text-muted">Hapus Item harus memasukkan PIN Atasan</small>
                            </div>
                        </div>

                        {{-- Harga Coret --}}
                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$price_crossed" wire:model="price_crossed" />
                            <div class="ms-2">
                                <div class="fw-semibold">Harga Coret Pada Nota</div>
                                <small class="text-muted">Harga asli pada struk dicoret dan harga grosir muncul</small>
                            </div>
                        </div>

                        {{-- Riwayat Transaksi --}}
                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$show_recent_sales" wire:model="show_recent_sales" />
                            <div class="ms-2">
                                <div class="fw-semibold">Tampilkan Menu Riwayat Transaksi</div>
                                <small class="text-muted">Fitur ini menampilkan penjualan terakhir di kasir</small>
                            </div>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$show_dashboard" wire:model="show_dashboard" />
                            <div class="ms-2">
                                <div class="fw-semibold">Tampilkan Dashboard</div>
                                <small class="text-muted">Fitur ini menampilkan Menu Dashboard pada halaman
                                    kasir</small>
                            </div>
                        </div>

                        {{-- Nominal Transaksi --}}
                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$show_nominal_transaction" wire:model="show_nominal_transaction" />
                            <div class="ms-2">
                                <div class="fw-semibold">Tampilkan Nominal Transaksi</div>
                                <small class="text-muted">Menampilkan total transaksi pada halaman riwayat</small>
                            </div>
                        </div>

                        {{-- Versi Retail Advance --}}
                        @if (config('app.current_version_product') == 'retail_advance')
                            <div class="mb-4 d-flex align-items-center">
                                <x-toggle-switch :is_checked="$show_detail_when_close_cashier" wire:model="show_detail_when_close_cashier" />
                                <div class="ms-2">
                                    <div class="fw-semibold">Tampilkan Detail Penjualan saat tutup kasir</div>
                                    <small class="text-muted">Menampilkan ringkasan transaksi saat tutup kasir</small>
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center">
                                <x-toggle-switch :is_checked="$rounding_enabled" wire:model="rounding_enabled" />
                                <div class="ms-2">
                                    <div class="fw-semibold">Pembulatan Nominal</div>
                                    <small class="text-muted">Mengaktifkan fitur pembulatan nilai total
                                        transaksi</small>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- ADMIN --}}
                    <div class="col-md-6">
                        <div class="mb-3 d-flex align-items-center">
                            <h3>Admin</h3>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$price_change" wire:model="price_change" />
                            <div class="ms-2">
                                <div class="fw-semibold">Notifikasi Perubahan Harga</div>
                                <small class="text-muted">Akan mengirimkan notifikasi saat ada perubahan harga</small>
                            </div>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$stock_alert" wire:model="stock_alert" />
                            <div class="ms-2">
                                <div class="fw-semibold">Notifikasi Stok Habis</div>
                                <small class="text-muted">Akan mengingatkan admin jika stok suatu produk habis</small>
                            </div>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$minus_price" wire:model="minus_price" />
                            <div class="ms-2">
                                <div class="fw-semibold">Harga Minus</div>
                                <small class="text-muted">Harga bisa bernilai minus saat input produk</small>
                            </div>
                        </div>

                        <div class="mb-4 d-flex align-items-center">
                            <x-toggle-switch :is_checked="$simple_purchase" wire:model="simple_purchase" />
                            <div class="ms-2">
                                <div class="fw-semibold">Pembelian Sederhana</div>
                                <small class="text-muted">Memungkinkan input langsung penerimaan barang (PN)</small>
                            </div>
                        </div>

                        @if (config('app.current_version_product') == 'retail_advance')
                            <div class="mb-4 d-flex align-items-center">
                                <x-toggle-switch :is_checked="$customer_price" wire:model="customer_price" />
                                <div class="ms-2">
                                    <div class="fw-semibold">Harga Berdasarkan Kategori Pelanggan</div>
                                    <small class="text-muted">Aktifkan jika ingin harga berbeda tiap jenis
                                        pelanggan</small>
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center">
                                <x-toggle-switch :is_checked="$show_and_change_order_status" wire:model="show_and_change_order_status" />
                                <div class="ms-2">
                                    <div class="fw-semibold">Tampilkan Status Pengiriman</div>
                                    <small class="text-muted">Fitur ini menampilkan dan ubah status pengiriman</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row row-cards mt-3">
        <div class="card">
            <div class="card-header h3">
                Opsi Tampilan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="label mb-2 fw-bold">
                                Tampilan Perubahan Kuantitas Produk :
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault"
                                    id="flexRadioDefault1" wire:model="change_qty_direct_after_add"
                                    {{ $change_qty_direct_after_add ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexRadioDefault1">
                                    Ganti Qty langsung pada list barang
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault"
                                    id="flexRadioDefault2" wire:model="change_qty_popup"
                                    {{ $change_qty_popup ? 'checked' : '' }}>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    Tampil Pop-Up Modal / dengan tombol F2
                                </label>
                            </div>
                        </div>

                        {{-- <h3 class="mt-3">Custom Footer Nota</h3>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css" />
                        <trix-editor wire:ignore id="trixEditor"></trix-editor>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

                        <button id='saveCustomFooter' class="btn btn-primary mt-3">Simpan Custom Footer</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    window.addEventListener('updated', function(e) {
        Toast.fire({
            icon: "success",
            title: e.detail.message,
            position: "top-end",
        });
    })

    document.addEventListener('livewire:load', function() {
        var trixEditor = document.getElementById('trixEditor');

        const customFooter = @json($this->custom_footer ?? '---');

        if (customFooter) {
            trixEditor.editor.loadHTML(customFooter);
        }

        const submitFooter = document.getElementById('saveCustomFooter');

        submitFooter.addEventListener('click', function() {
            const footerContent = trixEditor.value;
            @this.set('custom_footer', footerContent);
            @this.call('saveCustomFooter');
        });
    });
</script>
