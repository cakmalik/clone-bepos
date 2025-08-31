<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('layouts.nota.style-normal')
</head>

<body>
    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <div class="header-info">
                            @include('layouts.nota.kop')
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">Laporan Penjualan Berdasarkan Produk</h3>
                            <br>
                                @forelse($data[0] as $row)
                                    <table>
                                        <tr>
                                            <td style="width: 100px;"><b>Nama Produk</b></td>
                                            <td style="width: 10px;"><b>:</b></td>
                                            <td><b>{{ $row->product_name }}</b></td>
                                        </tr>
                                        <tr>
                                            <td><b>Outlet</b></td>
                                            <td><b>:</b></td>
                                            <td><b>{{ $row->outlet_name }}</b></td>
                                        </tr>
                                    </table>
                                    <br>

                                    <table>
                                        <thead>
                                            <tr class="border-cust">
                                                <th>Tanggal</th>
                                                <th>No. Invoice</th>
                                                <th>Qty</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                                <th>Laba</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($row->salesDetails as $detail)
                                                <tr class="border-lr">
                                                    <td class="center">{{ dateWithTime($detail->sale_date) }}</td>
                                                    <td class="">{{ $detail->sale_code }}</td>
                                                    <td class="center">{{ formatDecimal($detail->qty) }}</td>
                                                    <td class="right">{{ rupiah($detail->price) }}</td>
                                                    <td class="right">{{ rupiah($detail->subtotal) }}</td>
                                                    <td class="right">{{ rupiah($detail->profit) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <th class="right border-tp" colspan="4">Total</th>
                                                <th class="right border-tp border-rl">{{ rupiah($row->total_sales) }}</th>
                                                <th class="right border-tp border-rl">{{ rupiah($row->total_profit) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br><br>
                                @empty
                                    <h4 class="center">-- Tidak ada data ditemukan -- </h4>
                                @endforelse
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
        window.onload = function() {
            parent.iframeLoaded();
        }
    </script>
</body>

</html>
