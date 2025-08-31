<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetDynamicTitle
{
    public function handle(Request $request, Closure $next)
    {
        $titles = [
            'dashboard' => 'Dashboard',
            'dashboard/top-product' => 'Produk Terlaris',
            'dashboard/least-sold-product' => 'Produk Tidak Laku',
            'dashboard/top-category' => 'Kategori Terlaris',
            'dashboard/top-customer' => 'Pelanggan Terlaris',
            'dashboard/top-cashier' => 'Kasir Terlaris',
            'dashboard/top-payment-method' => 'Pembayaran Terbanyak',
            
            'product' => 'Produk',
            'product/product_category' => 'Kategori Produk',
            'brand' => 'Merk Produk',
            'product/product_unit' => 'Satuan Produk',
            'product/print-barcode' => 'Barcode Produk',
            'product/product_selling_price' => 'Harga Penjualan',
            'ProductDiscount' => 'Produk Diskon',
            
            'inventory' => 'Gudang',
            'report/inventory/stock_gudang' => 'Stok Gudang',
            'report/inventory/stock_outlet' => 'Stok Outlet',
            'stock_opname2' => 'Stok Opname',
            'stock_adjustment' => 'Stok Adjustment',
            'stock_mutation' => 'Mutasi Stok',
            'stock_history' => 'Riwayat Stok',

            'cash_proof_in' => 'Bukti Kas Masuk',
            'cash_proof_out' => 'Bukti Kas Keluar',

            'purchase_requisition' => 'Permintaan (PR)',
            'purchase_order' => 'Pemesanan (PO)',
            'purchase_reception' => 'Penerimaan (PN)',
            'purchase_invoice' => 'Faktur (INV)',
            'purchase_payment' => 'Pembayaran Faktur',
            'purchase_return' => 'Retur (RP)',
            'invoice_payment' => 'Pembayaran Faktur',

            'sales' => 'Riwayat Penjualan',
            'debt_payment' => 'Pembayaran Piutang',
            'retur-sales' => 'Retur Penjualan',
            'payment_method' => 'Metode Pembayaran',
            'discount' => 'Diskon',
            'cashflow_close' => 'Tutup Buku Kas',

            'report/inventory/stock_opname' => 'Laporan Stok Opname',
            'report/inventory/stock_adjustment' => 'Laporan Stok Adjustment',
            'report/inventory/stock_mutation' => 'Laporan Mutasi Stok',
            'report/purchase/purchase_order' => 'Laporan Pemesanan (PO)',
            'report/purchase/purchase_reception' => 'Laporan Penerimaan (PN)',
            'report/purchase/purchase_return' => 'Laporan Retur (RP)',
            'report/sales-overview' => 'Ringkasan Penjualan',
            'report/sales' => 'Laporan Penjualan',
            'report/laba-rugi' => 'Laporan Laba Rugi',
            'report/sales/void' => 'Laporan Pembatalan Penjualan',
            'report/stock-value-report' => 'Laporan Nilai Stok',

            'customer' => 'Pelanggan',
            'customer/customer_category' => 'Kategori Pelanggan',
            'supplier' => 'Supplier',
            'outlet' => 'Outlet',

            'settings/pos' => 'Pengaturan POS',
            'settings/report' => 'Pengaturan Laporan',
            'settings/report' => 'Pengaturan Stok Produk',

            'users' => 'Pengguna',
            'permission' => 'Hak Akses',
            'role' => 'Peran Pengguna',
            'profile_company' => 'Profil Perusahaan',
        ];

        // Ambil path lengkap tanpa parameter query
        $path = trim($request->path(), '/'); // contoh: customer/customer_category

        // Fallback ke segment pertama jika tidak ditemukan
        $prefix = $request->segment(1); // contoh: customer

        // Tentukan title berdasarkan path penuh atau segment pertama
        $title = $titles[$path] ?? $titles[$prefix] ?? null;

        view()->share('title', $title);

        return $next($request);
    }
}
