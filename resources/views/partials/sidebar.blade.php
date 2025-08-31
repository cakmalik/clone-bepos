@php
    $permissions = getMenuPermissions();
    use Illuminate\Support\Str;
@endphp

<aside class="navbar navbar-vertical navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            <img class="navbar-brand navbar-brand-autodark cust-brand"
                src="{{ asset('images/Kashir_Minimarket_Logo.png') }} " alt="{{ config('app.name') }}">
        </h1>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="navbar-nav pt-lg-3">

                {{-- Dashboard --}}
                @if (in_array('Dashboard', $permissions))
                    <li class="nav-item @if (Route::currentRouteName() == 'view.dashboard') active @endif">
                        <a class="nav-link" href="{{ route('view.dashboard') }}">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler-icons.io/i/dashboard -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="13" r="2" />
                                    <line x1="13.45" y1="11.55" x2="15.5" y2="9.5" />
                                    <path d="M6.4 20a9 9 0 1 1 11.2 0z" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Dashboard
                            </span>
                        </a>
                    </li>
                @endif

                {{-- Customer / Member --}}
                {{-- @if (in_array('Customer', $permissions))
                <li
                    class="nav-item @if (strpos(Route::currentRouteName(), 'customer') !== false) active @endif dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="true">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 11l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Pelanggan
                        </span>
                    </a>
                    <div class="dropdown-menu {{ Route::is(['customer*', 'membership*']) ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'customer.index') !== false) active @endif""
                                            href=" {{ route('customer.index') }}">
                                    Data Pelanggan
                                </a>
                            </div>
                            @if (in_array('Membership', $permissions))
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'membership.index') !== false) active @endif""
                                            href=" {{ route('membership.index') }}">
                                    Membership Pelanggan
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </li>
                @endif --}}

                {{-- Product --}}
                @if (in_array('Product', $permissions))
                    <li class="nav-item {{ Route::is('product*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-building-store" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="3" y1="21" x2="21" y2="21"></line>
                                    <path
                                        d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4">
                                    </path>
                                    <line x1="5" y1="21" x2="5" y2="10.85"></line>
                                    <line x1="19" y1="21" x2="19" y2="10.85"></line>
                                    <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Produk
                            </span>
                        </a>
                        <div
                            class="dropdown-menu {{ Route::is(['product*', 'brand*', 'ProductDiscount*', 'tiered_prices*', 'stock_mutation_reward*', 'loyalty_point*']) ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Product', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'product.index') !== false) active @endif"
                                            href=" {{ route('product.index') }}">
                                            Produk
                                        </a>
                                    @endif
                                    @if (in_array('Product Category', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'productCategory.index') !== false) active @endif"
                                            href=" {{ route('productCategory.index') }}">
                                            Kategori Produk
                                        </a>
                                    @endif
                                    @if (in_array('Brand', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'brand.index') !== false) active @endif"
                                            href="{{ route('brand.index') }}">
                                            Merk Produk
                                        </a>
                                    @endif
                                    @if (in_array('Product Unit', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'productUnit.index') !== false) active @endif"
                                            href=" {{ route('productUnit.index') }}">
                                            Satuan Produk
                                        </a>
                                    @endif
                                    @if (in_array('Cetak Barcode', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'product.print-barcode') !== false) active @endif"
                                            href=" {{ route('product.print-barcode') }}">
                                            Barcode Produk
                                        </a>
                                    @endif
                                    @if (in_array('Harga Bertingkat', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'tiered_prices.index') !== false) active @endif"
                                            href=" {{ route('tiered_prices.index') }}">
                                            Harga Bertingkat
                                        </a>
                                    @endif

                                    {{-- @if (in_array('Loyalty', $permissions))
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'loyalty_point.index') !== false) active @endif"
                                    href=" {{ route('loyalty_point.index') }}">
                                    Loyalty Point
                                </a>
                                @endif --}}
                                    {{-- @if (in_array('Product Stock', $permissions))
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'productStock.index') !== false) active @endif"
                                    href=" {{ route('productStock.index') }}">
                                    Stok Produk
                                </a>
                                @endif --}}
                                    @if (in_array('Product Selling Price', $permissions))
                                    <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'productSellingPrice.index') !== false) active @endif"
                                        href=" {{ route('productSellingPrice.index') }}">
                                        Harga Penjualan
                                    </a>
                                    @endif

                                    @if (in_array('Produk Diskon', $permissions) && config('app.current_version_product') == 'retail_advance')
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'ProductDiscount.index') !== false) active @endif"
                                            href=" {{ route('ProductDiscount.index') }}">
                                            Produk Diskon
                                        </a>
                                    @endif
                                    {{-- @if (in_array('Mutasi Hadiah', $permissions))
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'stock_mutation_reward.index') !== false) active @endif"
                                    href=" {{ route('stock_mutation_reward.index') }}">
                                    Mutasi Hadiah
                                </a>
                                @endif --}}
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Manajemen Stok --}}
                @if (in_array('Inventory', $permissions))
                    <li
                        class="nav-item
                                {{ Route::is('historyStock.*') ? 'active' : '' }}
                                {{ Route::is('stockOpname.*') ? 'active' : '' }}
                                {{ Route::is('stockAdjustment.*') ? 'active' : '' }}  dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z"></path>
                                    <path d="M2 13.5v5.5l5 3"></path>
                                    <path d="M7 16.545l5 -3.03"></path>
                                    <path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z"></path>
                                    <path d="M12 19l5 3"></path>
                                    <path d="M17 16.5l5 -3"></path>
                                    <path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5"></path>
                                    <path d="M7 5.03v5.455"></path>
                                    <path d="M12 8l5 -3"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Manajemen Stok
                            </span>
                        </a>
                        <div
                            class="dropdown-menu
                                {{ Route::is([
                                    'historyStock.index',
                                    'stockOpname.*',
                                    'stockAdjustment.*',
                                    'inventory.index',
                                    'stockMutation.*',
                                    'stockMutationInventoryToOutlet.*',
                                    'stock_mutation.*',
                                    'indexStockGudang.*',
                                    'indexStockOutlet.*',
                                ])
                                    ? 'show'
                                    : '' }}">

                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Inventory', $permissions))
                                        <a class="dropdown-item {{ Route::is('inventory.index') ? 'active' : '' }}"
                                            href=" {{ route('inventory.index') }}">
                                            Gudang
                                        </a>
                                    @endif
                                    @if (in_array('Laporan Stok Gudang', $permissions))
                                        <a class="dropdown-item {{ Request::is('report/inventory/stock_gudang') ? 'active' : '' }}"
                                            href="{{ url('report/inventory/stock_gudang') }}">
                                            Stok Gudang
                                        </a>
                                    @endif
                                    @if (in_array('Laporan Stok Outlet', $permissions))
                                        <a class="dropdown-item {{ Request::is('report/inventory/stock_outlet') ? 'active' : '' }}"
                                            href="{{ url('report/inventory/stock_outlet') }}">
                                            Stok Outlet
                                        </a>
                                    @endif
                                    @if (in_array('Stock Opname', $permissions))
                                        <a class="dropdown-item {{ Route::is('stockOpname.*') ? 'active' : '' }}"
                                            href=" {{ route('stockOpname.index2') }}">
                                            Stok Opname (SO)
                                        </a>
                                    @endif
                                    @if (in_array('Stock Adjustment', $permissions))
                                        <a class="dropdown-item {{ Route::is('stockAdjustment.index') ? 'active' : '' }}"
                                            href=" {{ route('stockAdjustment.index') }}">
                                            Stok Adjustment (ADJ)
                                        </a>
                                    @endif
                                    {{-- @if (in_array('Stock Mutation', $permissions))
                                <a class="dropdown-item {{ Route::is('stockMutation.index') ? 'active' : '' }}"
                                    href=" {{ route('stockMutation.index') }}">
                                    Stock Mutation
                                </a>
                                @endif --}}
                                    @if (in_array('Stock Mutation Inventory to Outlet', $permissions))
                                        <a class="dropdown-item {{ Route::is('stock_mutation.index-v2') ? 'active' : '' }}"
                                            href=" {{ route('stock_mutation.index-v2') }}">
                                            Mutasi Stok
                                        </a>
                                    @endif
                                    @if (in_array('Stock History', $permissions))
                                        <a class="dropdown-item {{ Route::is('historyStock.index') ? 'active' : '' }}"
                                            href=" {{ route('historyStock.index') }}">
                                            Riwayat Stok
                                        </a>
                                    @endif
                                    @if (in_array('Laporan Stok Konsolidasi Outlet', $permissions))
                                        <a class="dropdown-item {{ Request::is('report/inventory/stock_outlet/consolidation') ? 'active' : '' }}"
                                            href="{{ url('report/inventory/stock_outlet/consolidation') }}">
                                            Konsolidasi Stok Outlet
                                        </a>
                                    @endif
                                    @if (in_array('Laporan Stok Konsolidasi Gudang', $permissions))
                                        <a class="dropdown-item  {{ Request::is('report/inventory/stock_gudang/consolidation') ? 'active' : '' }}"
                                            href="{{ url('report/inventory/stock_gudang/consolidation') }}">
                                            Konsolidasi Stok Gudang
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Cash Proof --}}
                @if (in_array('Cash Proof In', $permissions))
                    <li class="nav-item {{ Request::is('cash_proof_*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z">
                                    </path>
                                    <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Bukti Kas
                            </span>
                        </a>
                        <div class="dropdown-menu @if (Request::is('cash_proof*')) {{ 'show' }} @endif">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Cash Proof In', $permissions))
                                        <a class="dropdown-item @if (Request::is('cash_proof_in*')) active @endif"
                                            href=" {{ route('cashProofIn.index') }}">
                                            Bukti Kas Masuk (BKM)
                                        </a>
                                    @endif
                                    @if (in_array('Cash Proof Out', $permissions))
                                        <a class="dropdown-item @if (Request::is('cash_proof_out*')) active @endif"
                                            href=" {{ route('cashProofOut.index') }}">
                                            Bukti Kas Keluar (BKK)
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Purchase --}}
                @if (in_array('Purchase', $permissions))
                    <li
                        class="nav-item  {{ Request::is('purchase_requisition*') ? 'active' : '' }} {{ Request::is('purchase_order*') ? 'active' : '' }} {{ Request::is('purchase_reception*') ? 'active' : '' }} {{ Request::is('purchase_invoice*') ? 'active' : '' }} {{ Request::is('invoice_payment*') ? 'active' : '' }} {{ Request::is('purchase_return*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-shopping-cart" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="6" cy="19" r="2"></circle>
                                    <circle cx="17" cy="19" r="2"></circle>
                                    <path d="M17 17h-11v-14h-2"></path>
                                    <path d="M6 5l14 1l-1 7h-13"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Pembelian
                            </span>
                        </a>
                        <div class="dropdown-menu @if (Request::is('purchase_requisition*') or
                                Request::is('purchase_order*') or
                                Request::is('purchase_reception*') or
                                Request::is('purchase_invoice*') or
                                Request::is('invoice_payment*') or
                                Request::is('purchase_return*')) {{ 'show' }} @endif">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (!session('simple_purchase'))
                                        @if (in_array('Purchase Requisition', $permissions))
                                            <a class="dropdown-item {{ Request::is('purchase_requisition*') ? 'active' : '' }}"
                                                href=" /purchase_requisition">
                                                Permintaan (PR)
                                            </a>
                                        @endif
                                    @endif
                                        @if (in_array('Purchase Order', $permissions))
                                            <a class="dropdown-item {{ Request::is('purchase_order*') ? 'active' : '' }}"
                                                href=" /purchase_order">
                                                Pemesanan (PO)
                                            </a>
                                        @endif
                                    @if (in_array('Purchase Reception', $permissions))
                                        <a class="dropdown-item {{ Request::is('purchase_reception*') ? 'active' : '' }}"
                                            href=" /purchase_reception">
                                            Penerimaan (PN)
                                        </a>
                                    @endif
                                    @if (in_array('Purchase Invoice', $permissions))
                                        <a class="dropdown-item {{ Request::is('purchase_invoice*') ? 'active' : '' }}"
                                            href=" /purchase_invoice">
                                            Faktur (INV)
                                        </a>
                                    @endif
                                    @if (in_array('Invoice Payment', $permissions))
                                        <a class="dropdown-item {{ Request::is('invoice_payment*') ? 'active' : '' }}"
                                            href=" /invoice_payment">
                                            Pembayaran Faktur
                                        </a>
                                    @endif
                                    @if (in_array('Purchase Return', $permissions))
                                        <a class="dropdown-item {{ Request::is('purchase_return*') ? 'active' : '' }}"
                                            href=" /purchase_return">
                                            Retur (RP)
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Transaksi --}}
                @if (in_array('Sales', $permissions))
                    <li
                        class="nav-item {{ Request::is('sales*') ? 'active' : '' }} {{ Request::is('paymentMethod.index*') ? 'active' : '' }} {{ Request::is('cashflowClose.index') ? 'active' : '' }}{{ Request::is('discount.index') ? 'active' : '' }} {{ Request::is('Retur Sales*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-shopping-cart-discount" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <circle cx="6" cy="19" r="2"></circle>
                                    <circle cx="17" cy="19" r="2"></circle>
                                    <path d="M17 17h-11v-14h-2"></path>
                                    <path d="M20 6l-1 7h-13"></path>
                                    <path d="M10 10l6 -6"></path>
                                    <circle cx="10.5" cy="4.5" r=".5"></circle>
                                    <circle cx="15.5" cy="9.5" r=".5"></circle>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Transaksi
                            </span>
                        </a>
                        <div class="dropdown-menu @if (Request::is('sales') or
                                Request::is('sales/*') or
                                Route::is('sales.index') or
                                Route::is('paymentMethod.index') or
                                Request::is('cashflow_close*') or
                                Route::is('discount.index') or
                                Route::is('debtPayment.*') or
                                 Request::is('retur-sales') or
                                Request::is('retur-sales/*')) {{ 'show' }} @endif">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Sales', $permissions))
                                        <a class="dropdown-item {{ Request::is('sales*') ? 'active' : '' }}"
                                            href="{{ route('sales.index') }}">
                                            Riwayat Penjualan
                                        </a>
                                    @endif
                                    @if (in_array('Debt Payment', $permissions))
                                        <a class="dropdown-item {{ Request::is('debt_payment*') ? 'active' : '' }}"
                                            href="{{ route('debtPayment.index') }}">
                                            Pembayaran Piutang
                                        </a>
                                    @endif
                                    @if (in_array('Retur Sales', $permissions))
                                        <a class="dropdown-item {{ Request::is('retur-sales*') ? 'active' : '' }}"
                                            href="{{ route('retur-sales.index') }}">
                                            Retur Penjualan
                                        </a>
                                    @endif
                                    @if (in_array('Payment Method', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'paymentMethod.index') !== false) active @endif"
                                            href="{{ route('paymentMethod.index') }}">
                                            Metode Pembayaran
                                        </a>
                                    @endif
                                    @if (in_array('Sales', getMenuPermissions()))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'discount.index') !== false) active @endif"
                                            href="{{ route('discount.index') }}">
                                            Diskon
                                        </a>
                                    @endif
                                    @if (in_array('Cashflow Close', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'cashflowClose.index') !== false) active @endif"
                                            href="{{ route('cashflowClose.index') }}">
                                            Tutup Buku Kas
                                        </a>


                                        <a class="dropdown-item"
                                            href="{{ route('working-hours') }}">
                                            Jam kerja
                                        </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </li>
                @endif

                {{-- Report --}}
                @if (in_array('Report', $permissions))
                    <li class="nav-item dropdown ">
                        <a class="nav-link dropdown-toggle " href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-description" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                    </path>
                                    <path d="M9 17h6"></path>
                                    <path d="M9 13h6"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title ">
                                Laporan
                            </span>
                        </a>
                        <div class="dropdown-menu @if (Request::is('report*')) {{ 'show' }} @endif">
                            @if (in_array('Report Inventory', $permissions))
                                <div class=" dropdown-menu-columns ">
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle " href="#sidebar-inventory-report"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                            aria-expanded="true">
                                            Manajemen Stok
                                        </a>
                                        <div
                                            class="dropdown-menu @if (Request::is('report/inventory*')) {{ 'show' }} @endif">
                                            <a href="{{ url('report/inventory/stock_opname') }}"
                                                class="dropdown-item {{ Request::is('report/inventory/stock_opname') ? 'active' : '' }}">Stok
                                                Opname (SO)</a>
                                            <a href="{{ url('report/inventory/stock_adjustment') }}"
                                                class="dropdown-item {{ Request::is('report/inventory/stock_adjustment') ? 'active' : '' }}">Stok
                                                Adjustment (ADJ)</a>
                                            <a href="{{ url('report/inventory/stock_mutation') }}"
                                                class="dropdown-item {{ Request::is('report/inventory/stock_mutation') ? 'active' : '' }}">Mutasi
                                                Stok</a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class=" dropdown-menu-columns ">
                                <div class="dropend">
                                    <a class="dropdown-item dropdown-toggle " href="#sidebar-purchase-report"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                        aria-expanded="true">
                                        Pembelian
                                    </a>
                                    <div
                                        class="dropdown-menu @if (Request::is('report/purchase*')) {{ 'show' }} @endif">
                                        @if (in_array('Report Purchase Order', $permissions))
                                            <a href="{{ url('report/purchase/purchase_order') }}"
                                                class="dropdown-item {{ Request::is('report/purchase/purchase_order') ? 'active' : '' }}">Pemesanan
                                                (PO)</a>
                                        @endif
                                        @if (in_array('Report Purchase Reception', $permissions))
                                            <a href="{{ url('report/purchase/purchase_reception') }}"
                                                class="dropdown-item {{ Request::is('report/purchase/purchase_reception') ? 'active' : '' }}">Penerimaan
                                                (PN)</a>
                                        @endif
                                        @if (in_array('Report Purchase Return', $permissions))
                                            <a href="{{ url('report/purchase/purchase_return') }}"
                                                class="dropdown-item {{ Request::is('report/purchase/purchase_return') ? 'active' : '' }}">Retur
                                                (RP)</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class=" dropdown-menu-columns ">
                                <div class="dropend">
                                    <a class="dropdown-item dropdown-toggle " href="#sidebar-sales-report"
                                        data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                        aria-expanded="true">
                                        Transaksi
                                    </a>
                                    <div
                                        class="dropdown-menu @if (Request::is('report/sales*') or Request::is('report/payment*') or Request::is('report/sales-return*')) {{ 'show' }} @endif">
                                        @if (in_array('Report Sales Overview', $permissions))
                                            <a href="{{ url('report/sales-overview') }}"
                                                class="dropdown-item {{ Request::is('report/sales-overview') ? 'active' : '' }}">Ringkasan
                                                Penjualan</a>
                                        @endif
                                        @if (in_array('Report Sales', $permissions))
                                            <a href="{{ url('report/sales') }}"
                                                class="dropdown-item {{ Request::is('report/sales') ? 'active' : '' }}">
                                                Penjualan</a>
                                        @endif
                                        @if (in_array('Report Laba Rugi', $permissions))
                                            <a href="{{ url('report/laba-rugi') }}"
                                                class="dropdown-item {{ Request::is('report/laba-rugi*') ? 'active' : '' }}">Laba
                                                Rugi</a>
                                        @endif
                                        @if (in_array('Report Void Penjualan', $permissions))
                                            <a href="{{ url('report/sales/void') }}"
                                                class="dropdown-item {{ Request::is('report/sales/return') ? 'active' : '' }}">Pembatalan Penjualan</a>
                                        @endif
                                        @if (in_array('Report Return', $permissions))
                                            <a href="{{ url('report/sales/return') }}"
                                                class="dropdown-item {{ Request::is('report/sales/return') ? 'active' : '' }}">Retur
                                                Penjualan</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (config('app.current_version_product') == 'retail_advance')
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'report.stock-value') !== false) active @endif""
                                    href=" {{ route('report.stock-value') }}">
                                    Nilai Stok
                                </a>
                            @endif
                        </div>
                    </li>
                @endif

                {{-- Accounting --}}
                @if (in_array('Accounting', $permissions))
                    <li
                        class="nav-item {{ Request::is('accounting') ? 'active' : '' }} {{ Request::is('accounting/journal_account') ? 'active' : '' }}  {{ Request::is('accounting/journal_account_type') ? 'active' : '' }} {{ Request::is('accounting/journal_type') ? 'active' : '' }} {{ Request::is('accounting/journal_transaction') ? 'active' : '' }} {{ Request::is('accounting/ledger') ? 'active' : '' }} {{ Request::is('accounting/journal_retur_sales') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-building-bank" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="3" y1="21" x2="21" y2="21"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                    <polyline points="5 6 12 3 19 6"></polyline>
                                    <line x1="4" y1="10" x2="4" y2="21"></line>
                                    <line x1="20" y1="10" x2="20" y2="21"></line>
                                    <line x1="8" y1="14" x2="8" y2="17"></line>
                                    <line x1="12" y1="14" x2="12" y2="17"></line>
                                    <line x1="16" y1="14" x2="16" y2="17"></line>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Akunting
                            </span>
                        </a>
                        <div class="dropdown-menu @if (Request::is('accounting') or Request::is('accounting/*')) {{ 'show' }} @endif">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Pembukuan
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/journal_account*') or
                                                        Request::is('accounting/journal_account_type*') or
                                                        Request::is('accounting/journal_type*')) {{ 'show' }} @endif">
                                                <a href="{{ url('accounting/journal_account') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_account*') ? 'active' : '' }}">Akun</a>
                                                <a href="{{ url('accounting/journal_account_type') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_account_type*') ? 'active' : '' }}">Tipe
                                                    Jurnal Akun</a>
                                                <a href="{{ url('accounting/journal_type') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_type*') ? 'active' : '' }}">Tipe
                                                    Jurnal</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Jurnal Penjualan
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/journal_retur_sales*')) {{ 'show' }} @endif">
                                                <a href="{{ url('accounting/journal_retur_sales') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_retur_sales*') ? 'active' : '' }}">Retur
                                                    Penjualan</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Jurnal Pembelian
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/journal_po*') or Request::is('accounting/journal_retur_purchase*')) {{ 'show' }} @endif">
                                                <a href="{{ url('accounting/journal_retur_purchase') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_retur_purchase*') ? 'active' : '' }}">Retur
                                                    Pembelian</a>
                                                <a href="{{ url('accounting/journal_po') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_po*') ? 'active' : '' }}">Pembayaran
                                                    PO/Hutang</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Stok
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/journal_adjustment*') or Request::is('accounting/stock/value-report*')) {{ 'show' }} @endif">
                                                <a href="{{ url('accounting/journal_adjustment') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_adjustment*') ? 'active' : '' }}">
                                                    Jurnal Adjustment</a>
                                                <a href="{{ url('accounting/stock/value-report') }}"
                                                    class="dropdown-item {{ Request::is('accounting/stock/value-report') ? 'active' : '' }}">Laporan
                                                    Nilai Stock</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Bukti Kas
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/cash_master*') or Request::is('accounting/cash_master*')) {{ 'show' }} @endif">
                                                <a href="{{ url('accounting/cash_master') }}"
                                                    class="dropdown-item {{ Request::is('accounting/cash_master*') ? 'active' : '' }}">
                                                    Master BKM/BKK</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="dropdown-menu-columns">
                                        <div class="dropend">
                                            <a class="dropdown-item dropdown-toggle" href="#sidebar-kas"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button"
                                                aria-expanded="true">
                                                Keuangan
                                            </a>
                                            <div
                                                class="dropdown-menu @if (Request::is('accounting/journal_transaction*') or
                                                        Request::is('accounting/ledger*') or
                                                        Request::is('accounting/profit_loss*')) {{ 'show' }} @endif ">
                                                <a href="{{ url('accounting/journal_stock_account') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_stock_account*') ? 'active' : '' }}">Set
                                                    Akun Barang</a>
                                                <a href="{{ url('accounting/journal_transaction') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_transaction*') ? 'active' : '' }}">Jurnal</a>
                                                <a href="{{ url('accounting/journal_closing') }}"
                                                    class="dropdown-item {{ Request::is('accounting/journal_closing*') ? 'active' : '' }}">Jurnal
                                                    Closing</a>
                                                <a href="{{ url('accounting/ledger') }}"
                                                    class="dropdown-item {{ Request::is('accounting/ledger*') ? 'active' : '' }}">Buku
                                                    Besar</a>
                                                <a href="{{ url('accounting/balance') }}"
                                                    class="dropdown-item {{ Request::is('accounting/balance*') ? 'active' : '' }}">Neraca</a>
                                                <a href="{{ url('accounting/profit_loss') }}"
                                                    class="dropdown-item {{ Request::is('accounting/profit_loss*') ? 'active' : '' }}">Laba
                                                    Rugi</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Cashier Machine --}}
                {{-- @if (in_array('Cashier Machine', $permissions))
                <li
                    class="nav-item @if (strpos(Route::currentRouteName(), 'cashier_machine') !== false) active @endif dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="true">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                <path d="M16 11l2 2l4 -4" />
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Mesin Kasir
                        </span>
                    </a>
                    <div
                        class="dropdown-menu @if (strpos(Route::currentRouteName(), 'cashier_machine') !== false) show @endif">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'cashier_machine.index') !== false) active @endif""
                                            href=" {{ route('cashier_machine.index') }}">
                                    Data Mesin Kasir
                                </a>
                            </div>
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'cashier_machine.create') !== false) active @endif""
                                            href=" {{ route('cashier_machine.create') }}">
                                    Tambah Mesin Kasir
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                @endif --}}

                {{-- Role --}}
                {{-- @if (in_array('Role', $permissions))
                <li
                    class="nav-item {{ Request::is('role') ? 'active' : '' }} {{ Route::is('role.index') ? 'active' : '' }} {{ Route::is('permission.index') ? 'active' : '' }} dropdown">
                    <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="true">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-access-off"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path
                                    d="M4 8v-2c0 -.554 .225 -1.055 .588 -1.417m-.588 11.417v2a2 2 0 0 0 2 2h2m8 -16h2a2 2 0 0 1 2 2v2m-4 12h2c.55 0 1.05 -.222 1.41 -.582m-4.41 -8.418a1 1 0 0 1 1 1m-.29 3.704a1 1 0 0 1 -.71 .296h-6a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1h2m-1 0v-1m1.182 -2.826a2 2 0 0 1 2.818 1.826v1m-11 -7l18 18">
                                </path>
                            </svg>
                        </span>
                        <span class="nav-link-title">
                            Peran & Hak Akses
                        </span>
                    </a>
                    <div
                        class="dropdown-menu @if (Request::is('role') or Route::is('role.index') or Route::is('permission.index')) {{ 'show' }} @endif">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @if (in_array('Role', $permissions))
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'role.index') !== false) active @endif""
                                                href=" {{ route('role.index') }}">
                                    Data Peran
                                </a>
                                @endif
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'menu.create') !== false) active @endif"" href="
                                    {{ route('menu.create') }}">
                                    Tambah Menu
                                </a>
                                @if (in_array('Permission', $permissions))
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'permission.index') !== false) active @endif""
                                                    href=" {{ route('permission.index') }}">
                                    Data Hak Akses
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
                @endif --}}

                {{-- Supplier --}}
                @if (in_array('Supplier', $permissions))
                    <li
                        class="nav-item  {{ Route::is('supplier.create*') ? 'active' : '' }} {{ Route::is('supplier.index*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                                <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-down">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4c.342 0 .674 .043 .99 .124" />
                                    <path d="M19 16v6" />
                                    <path d="M22 19l-3 3l-3 -3" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Supplier
                            </span>
                        </a>
                        <div class="dropdown-menu @if (strpos(Route::currentRouteName(), 'supplier') !== false) show @endif">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'supplier.index') !== false) active @endif""
                                        href=" {{ route('supplier.index') }}">
                                        Supplier
                                    </a>
                                    {{-- <a
                                    class="dropdown-item @if (strpos(Route::currentRouteName(), 'supplier.create') !== false) active @endif""
                                            href=" {{ route('supplier.create') }}">
                                    Tambah Supplier
                                </a> --}}
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Pelanggan --}}
                @if (in_array('Customer', $permissions))
                    <li class="nav-item @if (strpos(Route::currentRouteName(), 'customer') !== false) active @endif dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <!-- Download SVG icon from http://tabler-icons.io/i/user-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    <path d="M16 11l2 2l4 -4" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Pelanggan
                            </span>
                        </a>
                        <div class="dropdown-menu {{ Route::is(['customer*', 'membership*']) ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Customer', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'customer.index') !== false) active @endif""
                                            href=" {{ route('customer.index') }}">
                                            Pelanggan
                                        </a>
                                    @endif
                                    @if (in_array('Customer Category', $permissions))
                                        <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'customerCategory.index') !== false) active @endif""
                                            href=" {{ route('customerCategory.index') }}">
                                            Kategori Pelanggan
                                        </a>
                                    @endif
                                </div>
                                {{-- @if (in_array('Membership', $permissions))
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item @if (strpos(Route::currentRouteName(), 'membership.index') !== false) active @endif""
                                            href=" {{ route('membership.index') }}">
                                    Membership Pelanggan
                                </a>
                            </div>
                            @endif --}}
                            </div>
                        </div>
                    </li>
                @endif

                {{-- Outlet --}}


                @if (in_array('Outlet', $permissions))
                    <li class="nav-item {{ request()->is('outlet*') ? 'active' : '' }} dropdown">
                        <a class="nav-link dropdown-toggle" href="#navbar-layout" data-bs-toggle="dropdown"
                            data-bs-auto-close="false" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-building-skyscraper" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="3" y1="21" x2="21" y2="21"></line>
                                    <path d="M5 21v-14l8 -4v18"></path>
                                    <path d="M19 21v-10l-6 -4"></path>
                                    <line x1="9" y1="9" x2="9" y2="9.01"></line>
                                    <line x1="9" y1="12" x2="9" y2="12.01"></line>
                                    <line x1="9" y1="15" x2="9" y2="15.01"></line>
                                    <line x1="9" y1="18" x2="9" y2="18.01"></line>
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                Outlet
                            </span>
                        </a>
                        <div class="dropdown-menu {{ request()->is('outlet*') ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (Auth()->user()->role->role_name == 'SUPER SUPERADMIN')
                                        <a class="dropdown-item {{ request()->is('outlet/create') ? 'active' : '' }}"
                                            href="{{ route('outlet.create') }}">
                                            Tambah Outlet
                                        </a>
                                    @endif
                                    <a class="dropdown-item {{ request()->is('outlet') ? 'active' : '' }}"
                                        href="{{ route('outlet.index') }}">
                                        Outlet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endif


                {{-- Pengaturan --}}
                @if (in_array('Settings', $permissions))
                    <li
                        class="nav-item dropdown {{ Request::is('setting*') || Route::is(['settingProductStock.index', 'setting.pos.index', 'profileCompany.*']) ? 'active show' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="true">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z">
                                    </path>
                                    <path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                </svg>
                            </span>
                            <span class="nav-link-title">Pengaturan</span>
                        </a>
                        <div
                            class="dropdown-menu {{ Request::is('setting*') || Route::is(['settingProductStock.index', 'setting.pos.index', 'profileCompany.*', 'users.index', 'role.index', 'permission.index']) ? 'show' : '' }}">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    @if (in_array('Setting Stock Reminder', $permissions))
                                        <a class="dropdown-item {{ Route::is('setting.pos.index') ? 'active' : '' }}"
                                            href="{{ route('setting.pos.index') }}">POS</a>
                                    @endif
                                    @if (in_array('Setting Report', $permissions) && config('app.current_version_product') == 'retail_advance')
                                        <a class="dropdown-item {{ Route::is('setting.report') ? 'active' : '' }}"
                                            href="{{ route('setting.report') }}">Laporan</a>
                                    @endif
                                    @if (in_array('Setting Product Stock', $permissions))
                                        <a class="dropdown-item {{ Route::is('settingProductStock.index') ? 'active' : '' }}"
                                            href="{{ route('settingProductStock.index') }}">Stok Produk</a>
                                    @endif
                                    @if (in_array('User', $permissions))
                                        <a class="dropdown-item {{ Route::is('users.index') ? 'active' : '' }}"
                                            href="{{ route('users.index') }}">Pengguna</a>
                                    @endif
                                    @if (in_array('Permission', $permissions))
                                        <a class="dropdown-item {{ Route::is('permission.index') ? 'active' : '' }}"
                                            href="{{ route('permission.index') }}">Hak Akses</a>
                                    @endif
                                    @if (in_array('Role', $permissions))
                                        <a class="dropdown-item {{ Route::is('role.index') ? 'active' : '' }}"
                                            href="{{ route('role.index') }}">Peran Pengguna</a>
                                    @endif
                                    @if (in_array('Profile Company', $permissions))
                                        <a class="dropdown-item {{ Route::is('profileCompany.index') ? 'active' : '' }}"
                                            href="{{ route('profileCompany.index') }}">Profil</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</aside>
