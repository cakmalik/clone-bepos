<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 5mm;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px;
        }

        .header h2 {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 5px 3px;
            text-align: left;
            font-size: 12px;
        }

        .total {
            font-weight: bold;
        }

        .header {
            text-align: center;
            border: none !important;
        }

        .header p {
            margin: 2px 0;
            /* Mengurangi jarak antar paragraf */
            padding: 0;
        }

        .w-10 {
            width: 20%;
        }
    </style>
</head>

<body>
    <div style="text-align: center">
        {{-- <img src="{{ asset('path/to/company-logo.png') }}" alt="Company Logo"> --}}
        <div class="header">
            <h3>{{ profileCompany()->name }}</h3>
            <p>{{ profileCompany()->address }}</p>
            <p>{{ profileCompany()->email . ' ' . profileCompany()->phone }}</p>
        </div>
        <hr>
        <h2>Laporan Stok Nilai</h2>
    </div>

    <table style="margin-bottom: 20px;">
        <tr>
            <td class="w-10"><strong>Tanggal Laporan</strong></td>
            <td>{{ $date }}</td>
            <td class="w-10"><strong>Nilai Stok</strong></td>
            <td>Rp{{ number_format($total_stock_value) }}</td>
        </tr>
        <tr>
            <td class="w-10"><strong>Gudang</strong></td>
            <td>{{ $inventory }}</td>
            <td class="w-10"><strong>Potensi Nilai</strong></td>
            <td>Rp{{ number_format($total_potential_value) }}</td>

        </tr>
        <tr>
            <td class="w-10"><strong>Outlet</strong></td>
            <td>{{ $outlet }}</td>
            <td></td>
            <td></td>
        </tr>

    </table>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Produk</th>
                {{-- <th>Kategori</th> --}}
                <th>Outlet</th>
                <th>Gudang</th>
                <th>Stok Awal</th>
                <th>Pembelian</th>
                <th>Penjualan</th>
                <th>Stok Akhir</th>
                <th>Harga Beli</th>
                <th>Nilai Stok</th>
                <th>Harga Jual</th>
                <th>Potensi Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $stock)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $stock->product->name ?? '-' }}</td>
                {{-- <td>{{ $stock->productCategory->name ?? '-' }}</td> --}}
                <td>{{ $stock->outlet->name ?? '-' }}</td>
                <td>{{ $stock->inventory->name ?? '-' }}</td>
                <td>{{ $stock->initial_stock }}</td>
                <td>{{ number_format($stock->purchases) }}</td>
                <td>{{ $stock->sales }}</td>
                <td>{{ $stock->final_stock }}</td>
                <td>{{ number_format($stock->purchase_price) }}</td>
                <td>{{ number_format($stock->stock_value) }}</td>
                <td>{{ number_format($stock->selling_price) }}</td>
                <td>{{ number_format($stock->potential_value) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
