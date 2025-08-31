@extends('layouts.app')
@push('styles')
    <style>
        .hover-underline:hover {
            text-decoration: underline
        }
    </style>
@endpush

@section('page')
    <div class="container">
        <!-- Page title -->

        <div class="page-header d-print-none">
            <div class="row align-items-center row-cards">
                <div class="col">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Judul -->
                        <h2 class="page-title mb-0">
                            Dashboard
                        </h2>
                
                        <!-- Tanggal -->
                        <div class="card bg-white mb-0"
                            style="border-radius: .5rem; min-width: 250px; cursor: pointer;"
                            id="dateRangeTrigger">
                            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                                <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                                <span class="text-dark fw-bold" id="dateRangeText"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                @if ($settingPriceChange->value && $priceChange->isNotEmpty())
                    @include('pages.product.product_price.price_changes')
                @endif

                {{-- grafik sales --}}
                <div class="col-12 mt-3">
                    <div class="card" style="height: 28rem; border-radius: 1rem">
                        <div class="card-body ">
                            <div class="divide-y" id="grafik_line_sales">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end grafik --}}

                <div class="col-12 mt-3">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <a href="#" class="link-box-dashboard text-decoration-none">
                                <div class="card card-md" style="border-radius: 1rem">
                                    <div class="card-status-bottom bg-warning py-1" style="border-radius: 0 0 1rem 1rem;"></div>
                                    <div class="card-body py-2 px-3">
                                        <div class=" mb-2">
                                            <h4 class="text-secondary mb-1">Total Pendapatan</h4>
                                        </div>
                                        <div class="d-flex align-items-center pb-3">
                                            <div class="ms-auto">
                                                <h1 class="text-dark mb-0" id="netIncome">Rp. 0</h1>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                            </a>
                        </div>
                
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-md" style="border-radius: 1rem">
                                <div class="card-status-bottom bg-teal py-1" style="border-radius: 0 0 1rem 1rem;"></div>
                                <div class="card-body py-2 px-3">
                                    <div class=" mb-2">
                                        <h4 class="text-secondary mb-1">Total Laba</h4>
                                    </div>
                                    <div class="d-flex align-items-center pb-3">
                                        <div class="ms-auto">
                                            <h1 class="text-dark mb-0" id="netProfit">Rp. 0</h1>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-md" style="border-radius: 1rem">
                                <div class="card-status-bottom bg-blue py-1" style="border-radius: 0 0 1rem 1rem;"></div>
                                <div class="card-body py-2 px-3">
                                    <div class=" mb-2">
                                        <h4 class="text-secondary mb-1">Total Transaksi</h4>
                                    </div>
                                    <div class="d-flex align-items-center pb-3">
                                        <div class="ms-auto">
                                            <h1 class="text-dark mb-0" id="totalTransactions">0</h1>
                                        </div>
                                    </div>                                                                                                
                                </div>
                            </div>
                        </div>
                
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-md" style="border-radius: 1rem">
                                <div class="card-status-bottom bg-info py-1" style="border-radius: 0 0 1rem 1rem;"></div>
                                <div class="card-body py-2 px-3">
                                    <div class=" mb-2">
                                        <h4 class="text-secondary mb-1">Produk Terjual</h4>
                                    </div>
                                    <div class="d-flex align-items-center pb-3">
                                        <div class="ms-auto">
                                            <h1 class="text-dark mb-0" id="totalProductSold">0</h1>
                                        </div>
                                    </div>    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h3 class="d-inline">Statistik Penjualan</h3>
                        <small class="text-muted d-block" id="salesStatistic"></small>
                    </div>
                </div>
                

                <div class="col-12 mt-3">
                    <div class="row row-cards">
                        <div class="col-md-6">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.top-product') }}" class="text-secondary hover-underline">
                                            Produk Terlaris <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>

                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Nama Produk</th>
                                                <th width="40%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="topProduct">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.top-customer') }}" class="text-secondary hover-underline">
                                            Pelanggan Teratas <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Nama</th>
                                                <th width="40%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="topCustomers">
                                           
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <div class="row row-cards">
                        <div class="col-md-4">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.top-category') }}" class="text-secondary hover-underline">
                                            Kategori Terlaris <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Nama Kategori</th>
                                                <th width="40%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="topCategories">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.top-cashier') }}" class="text-secondary hover-underline">
                                            Kasir Terlaris <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Nama Kasir</th>
                                                <th width="40%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="topCashiers">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.top-payment-method') }}" class="text-secondary hover-underline">
                                            Pembayaran Terbanyak <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Metode Pembayaran</th>
                                                <th width="40%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="topPaymentMethods">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card" style="border-radius: 1rem">
                                <div class="card-body">
                                    <h4>
                                        <a href="{{ route('dashboard.least-sold-product') }}" class="text-secondary hover-underline">
                                            Produk Tidak Laku <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </h4>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th width="50%">Nama Produk</th>
                                                <th width="12%" class="text-center">QTY Terjual</th>
                                                <th width="12%" class="text-center">Terakhir Terjual</th>
                                                <th width="12%" class="text-center">Sisa Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody id="leastSoldProducts">
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            {{-- SEMENTARA HIDE DULU --}}

            {{-- @php
            $dueDateInvoices = dueDateInvoice();
            @endphp
            @if ($dueDateInvoices->isNotEmpty())
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>Transaksi Mendekati Jatuh Tempo</h4>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Transaksi</th>
                                            <th>Customer</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Total Tagihan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dueDateInvoices as $invoice)
                                            <tr>
                                                <td><a
                                                        href="{{ route('debtPayment.create', $invoice->id) }}">{{ $invoice->sale_code }}</a>
                                                </td>
                                                <td>{{ $invoice->customer_name }}</td>
                                                <td>{{ dateStandar($invoice->due_date) }}</td>
                                                <td>{{ rupiah($invoice->final_amount - $invoice->total_payment) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif --}}

            {{-- <div class="col-12 mt-3">
                <div class="row row-cards">
                    <div class="col-12">
                        <div class="row row-cards mb-4">
                            <span>
                                <h2>Informasi Pembelian</h2>
                            </span>
                            <div class="col-sm-6 col-lg-3">
                                <a href="{{ url('/purchase_requisition') }}" class="link-box-dashboard">
                                    <div class="card card-sm text-dark">
                                        <div class="card-status-bottom bg-dark py-1"></div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3>Draft Permintaan Pembelian</h3>
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-white text-dark avatar">
                                                        <i class="fas fa-clipboard fa-lg"></i>
                                                        <div class="col">
                                                            <div class="font-weight-medium mt-2">
                                                                <h1>{{ $draft_pr }}</h1>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="{{ url('/purchase_order') }}" class="link-box-dashboard">
                                    <div class="card card-sm text-dark">
                                        <div class="card-status-bottom bg-dark py-1"></div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3>Draft Pesanan Pembelian</h3>
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-white text-dark avatar">
                                                        <i class="fas fa-list-check fa-lg"></i>
                                                        <div class="col">
                                                            <div class="font-weight-medium mt-2">
                                                                <h1>{{ $draft_po }}</h1>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="{{ url('/purchase_reception') }}" class="link-box-dashboard">
                                    <div class="card card-sm text-dark">
                                        <div class="card-status-bottom bg-dark py-1"></div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3>Draft Penerimaan Barang</h3>
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-white text-dark avatar">
                                                        <i class="fas fa-receipt fa-lg"></i>
                                                        <div class="col">
                                                            <div class="font-weight-medium mt-2">
                                                                <h1>{{ $draft_receipt }}</h1>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a href="{{ url('/purchase_return') }}" class="link-box-dashboard">
                                    <div class="card card-sm text-dark">
                                        <div class="card-status-bottom bg-dark py-1"></div>
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h3>Draft Retur Pembelian</h3>
                                                </div>
                                            </div>
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <span class="bg-white text-dark avatar">
                                                        <i class="fas fa-exchange-alt fa-lg"></i>
                                                        <div class="col">
                                                            <div class="font-weight-medium mt-2">
                                                                <h1>{{ $draft_retur }}</h1>
                                                            </div>
                                                        </div>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">INFORMASI PEMBELIAN</h3>
                            </div>
                            <div class="card-body border-bottom py-3">
                                <div class="table-responsive">
                                    <table
                                        class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">#</th>
                                                <th>Informasi</th>
                                                <th>Keterangan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            @if ($total_pr)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>{{ $total_pr->total_data }} PR Belum dibuatkan PO</td>
                                                    <td>Antara tanggal <span
                                                            style="color :red">{{ dateStandar($total_pr->start) }}</span>
                                                        sampai
                                                        <span style="color :red">{{ dateStandar($total_pr->end) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/purchase_order') }}"
                                                            class="badge badge-sm bg-yellow">
                                                            Perlu Tindakan
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if ($total_po_not_received)
                                                <tr>
                                                    <td>{{ ++$no }}</td>
                                                    <td>{{ $total_po_not_received->total_data }} PO Belum dibuatkan
                                                        Penerimaan
                                                    </td>
                                                    <td>Antara tanggal <span
                                                            style="color :red">{{ dateStandar($total_po_not_received->start) }}</span>
                                                        sampai <span
                                                            style="color :red">{{ dateStandar($total_po_not_received->end) }}</span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/purchase_reception') }}"
                                                            class="badge badge-sm bg-yellow">
                                                            Perlu Tindakan
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if ($total_po_not_invoiced)
                                                <tr>
                                                    <td>{{ ++$no }}</td>
                                                    <td>{{ $total_po_not_invoiced->total_data }} PO Belum mendapatkan
                                                        invoice
                                                    </td>
                                                    <td>Antara tanggal <span
                                                            style="color :red">{{ dateStandar($total_po_not_invoiced->start) }}</span>
                                                        sampai <span
                                                            style="color :red">{{ dateStandar($total_po_not_invoiced->end) }}</span>
                                                    </td>
                                                    <td>
                                                        <span href="{{ url('/purchase_invoice') }}"
                                                            class="badge badge-sm bg-yellow">
                                                            Perlu Tindakan
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                <div class="col-12 mt-3">
                    <div class="row row-cards">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="border border-dark text-dark avatar bg-white">
                                                <i class="fas fa-store fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <b>Pembelian Berhasil</b>
                                            </div>
                                            <div class="text-muted">
                                                {{ $jumlahPembelianBerhasil ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="border border-dark text-dark avatar bg-white">
                                                <i class="fas fa-box fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <b>Kategori Produk</b>
                                            </div>
                                            <div class="text-muted">
                                                {{ $jumlahKategoriProduk ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="border border-dark text-dark avatar">
                                                <i class="fas fa-cart-plus fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <b>Jumlah Produk</b>
                                            </div>
                                            <div class="text-muted">
                                                {{ $jumlahProduk ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="border border-dark text-dark avatar">
                                                <i class="fas fa-chart-line fa-lg"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                <b>Penjualan Berhasil</b>
                                            </div>
                                            <div class="text-muted">
                                                {{ $successSales ?? 0 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>
            </div> --}}
        </div>
    </div>
  
@endsection
@push('scripts')
    <script>
        $(function () {
            const $dateRangeTrigger = $('#dateRangeTrigger');
            const $dateRangeText = $('#dateRangeText');
            const $salesStatistic = $('#salesStatistic');

            const start = moment().startOf('month');
            const end = moment().endOf('month');

            function fetchDashboardData(startDate, endDate) {
                $dateRangeText.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));
                $salesStatistic.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));


                $.ajax({
                    url: '/dashboard/data',
                    method: 'GET',
                    data: {
                        start_date: startDate.format('YYYY-MM-DD'),
                        end_date: endDate.format('YYYY-MM-DD')
                    },
                    success: function(response) {
                        // console.log(response);
                        //  Net Income
                        const netIncome = new countUp.CountUp('netIncome', response.netIncome, {
                            prefix: 'Rp. ',
                            separator: '.',
                            decimal: ','
                        });
                        if (!netIncome.error) netIncome.start();

                        // Net Profit
                        const netProfit = new countUp.CountUp('netProfit', response.netProfit, {
                            prefix: 'Rp. ',
                            separator: '.',
                            decimal: ','
                        });
                        if (!netProfit.error) netProfit.start();

                        // Total Transaksi
                        const totalTransactions = new countUp.CountUp('totalTransactions', response.totalTransactions);
                        if (!totalTransactions.error) totalTransactions.start();

                        // Total Produk Terjual
                        const totalProductSold = new countUp.CountUp('totalProductSold', response.totalProductSold);
                        if (!totalProductSold.error) totalProductSold.start();

                        // Produk Terlaris
                        let topProduct = '';
                        response.topProduct.forEach((item, index) => {
                            topProduct += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.product_name}</td>
                                <td class="text-end">${item.transaction_count.toLocaleString()} Transaksi</td>
                            </tr>`;
                        });
                        $('#topProduct').html(topProduct || `<tr><td colspan="3" class="text-center">Belum ada data</td></tr>`);

                        // Pelanggan Teratas
                        let topCustomers = '';
                        response.topCustomers.forEach((item, index) => {
                            topCustomers += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-end">${item.transaction_count} Transaksi</td>
                            </tr>`;
                        });
                        $('#topCustomers').html(topCustomers || `<tr><td colspan="3" class="text-center">Belum ada data</td></tr>`);

                        // Kategori Terlaris
                        let topCategories = '';
                        response.topCategories.forEach((item, index) => {
                            topCategories += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.category_name}</td>
                                <td class="text-end">${item.transaction_count.toLocaleString()} Transaksi</td>
                            </tr>`;
                        });
                        $('#topCategories').html(topCategories || `<tr><td colspan="3" class="text-center">Belum ada data</td></tr>`);

                        // Kasir Terlaris
                        let topCashiers = '';
                        response.topCashiers.forEach((item, index) => {
                            topCashiers += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.cashier_name}</td>
                                <td class="text-end">${item.transaction_count.toLocaleString()} Transaksi</td>
                            </tr>`;
                        });
                        $('#topCashiers').html(topCashiers || `<tr><td colspan="3" class="text-center">Belum ada data</td></tr>`);

                        // Metode Pembayaran Terlaris
                        let topPaymentMethods = '';
                        response.topPaymentMethods.forEach((item, index) => {
                            topPaymentMethods += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.payment_method}</td>
                                <td class="text-end">${item.transaction_count.toLocaleString()} Transaksi</td>
                            </tr>`;
                        });
                        $('#topPaymentMethods').html(topPaymentMethods || `<tr><td colspan="3" class="text-center">Belum ada data</td></tr>`);

                        // Produk Tidak Laku
                        let leastSoldProducts = '';
                        response.leastSoldProducts.forEach((item, index) => {
                            const terakhirTerjual = item.terakhir_terjual
                                ? (() => {
                                    const d = new Date(item.terakhir_terjual);
                                    const day = d.getDate().toString().padStart(2, '0');
                                    const month = (d.getMonth() + 1).toString().padStart(2, '0');
                                    const year = d.getFullYear();
                                    return `${day}-${month}-${year}`;
                                })()
                                : '-';

                            leastSoldProducts += `<tr>
                                <td>${index + 1}</td>
                                <td>${item.name}</td>
                                <td class="text-center" width="12%">${parseFloat(item.qty_terjual)}</td>
                                <td class="text-center" width="12%">${terakhirTerjual}</td>
                                <td class="text-end">${parseFloat(item.remaining_stock) + ' ' + item.unit_name}</td>
                            </tr>`;
                        });

                        $('#leastSoldProducts').html(leastSoldProducts || `<tr><td colspan="4" class="text-center">Belum ada data</td></tr>`);

                    }
                });
            }

            $dateRangeTrigger.daterangepicker({
                opens: 'left',
                locale: {
                    format: 'DD MMM YYYY',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    customRangeLabel: 'Rentang Kustom',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                },
                startDate: start,
                endDate: end,
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function(start, end) {
                fetchDashboardData(start, end);
            });

            // 🔥 Jalankan saat pertama kali halaman dimuat
            fetchDashboardData(start, end);
        });
    </script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        $('.table').DataTable({
            searching: false,
            paging: false,
            destroy: true,
            order: false,
            bInfo: false,
        });

        var total_pendapatan = {!! json_encode($totalPendapatanOmzet) !!};
        var month_6_bulan_terakhir = {!! json_encode($month6BulanTerakhir) !!};

        Highcharts.chart('grafik_line_sales', {
            title: {
                text: 'Grafik Penjualan Per 6 bulan terakhir'
            },
            xAxis: {
                //categories:  ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                categories: month_6_bulan_terakhir,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah Omset'
                },
                labels: {
                    formatter: function() {
                        return 'Rp ' + Highcharts.numberFormat(this.value, 0, ',', '.');
                    },
                    style: {
                        color: '#666666',
                        fontWeight: 'bold'
                    }
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>Rp. {point.y}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true
                    }
                }
            },
            series: [{
                type: 'areaspline',
                name: 'Total pendapatan perbulan',
                //data:[5000000, 6000000, 8000000, 7000000, 9000000, 10000000],
                data: total_pendapatan,
                color: '#50C878',
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.color('#50C878').setOpacity(0.5).get('rgba')],
                        [1, Highcharts.color('#50C878').setOpacity(0).get('rgba')]
                    ]
                }
            }]

        });


        var top_product = {!! json_encode($topProductChartData) !!};
        Highcharts.chart('grafik_barang', {
            chart: {
                type: 'pie',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                events: {
                    render: function() {
                        var chart = this,
                            center = chart.series[0].center,
                            titleBox = chart.title.getBBox(),
                            titleY = titleBox.y + titleBox.height / 2,
                            centerY = center[1] + chart.plotTop,
                            titleX = titleBox.x + titleBox.width / 2,
                            centerX = center[0] + chart.plotLeft,
                            title = chart.renderer.text('Penjualan 6 Bulan Terakhir', titleX, titleY)
                            .attr({
                                align: 'center',
                                class: 'donut-title'
                            })
                            .add(),
                            subtitle = chart.renderer.text('Barang yang Paling Laku', titleX, titleY + 20)
                            .attr({
                                align: 'center',
                                class: 'donut-subtitle'
                            })
                            .add(),
                            innerCircle = chart.renderer.circle(centerX, centerY, center[2] * 0.6)
                            .attr({
                                fill: '#fff'
                            })
                            .add();
                    }
                }
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            plotOptions: {
                pie: {
                    innerSize: '60%',
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y}'
                    }
                }
            },
            series: [{
                name: 'Penjualan',
                // data: [
                //     ['Lemari', 100],
                //     ['Kasur', 75],
                //     ['Jas Hujan', 50],
                //     ['Sendok', 25],
                //     ['Garpu', 10],
                //     ['Piring', 5]
                // ]
                data: top_product,
            }]
        });


        $(document).ready(function() {
            var modal = $('#notif');
            modal.modal('show');
        });
    </script>
@endpush
