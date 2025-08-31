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
                            @forelse($data as $outlet)
                            <table>
                                <tr>
                                    <td style="width: 100px;"><b>Outlet</b></td>
                                    <td style="width: 10px;"><b>:</b></td>
                                    <td><b>{{ $outlet['outletName'] }}</b></td>
                                </tr>
                            </table>
                            <br>
                        
                            <table>
                                <thead>
                                    <tr class="border-cust">
                                        <th>Nama Produk</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($outlet['products'] as $product)

                                        @php

                                        $qty = formatDecimal($product['total_quantity']);
                                        $total = formatDecimal($product['total_sales']);

                                        @endphp

                                        <tr class="border-lr">
                                            <td>{{ $product['product_name'] }}</td>
                                            <td class="center">{{ $qty }}</td>
                                            <td class="right">{{ rupiah($total) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th class="right border-tp" colspan="2">Total</th>
                                        <th class="right border-tp border-rl">
                                            {{ rupiah(formatDecimal(collect($outlet['products'])->sum('total_sales'))) }}
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                            <br><br>
                        @empty
                            <h4 class="center">-- Tidak ada data ditemukan --</h4>
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
