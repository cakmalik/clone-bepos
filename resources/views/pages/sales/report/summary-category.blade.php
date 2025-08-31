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
                            <h3 class="report-title">Laporan Penjualan By Kategori ({{ $data[1] }})</h3>

                            <table>
                                <tr class="border-cust">
                                    <th class="center">KATEGORI</th>
                                    <th class="center">QTY</th>
                                    <th>SUB HARGA BELI</th>
                                    <th>OMSET</th>
                                    <th>LABA</th>
                                </tr>
                                <?php
                                $total_subtotal = 0;
                                $total_subtotal_hpp = 0;
                                $total_profit = 0;
                                $total_qty = 0;
                                ?>


                                @forelse($data[0] as $item)
                                    <?php

                                    $total_qty += $item->qty;
                                    $total_subtotal_hpp += $item->subtotal_hpp;
                                    $total_subtotal += $item->subtotal;
                                    $total_profit += $item->final_profit;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->product_category }}</td>
                                        <td class="center">{{ formatDecimal($item->qty) }}</td>
                                        <td class="right">{{ rupiah(formatDecimal($item->subtotal_hpp)) }}</td>
                                        <td class="right">{{ rupiah(formatDecimal($item->subtotal)) }}</td>
                                        <td class="right">{{ rupiah(formatDecimal($item->final_profit)) }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="" class="center">TOTAL</th>
                                    <th>{{ formatDecimal($total_qty) }}</th>
                                    <th class="right">{{ rupiah(formatDecimal($total_subtotal_hpp)) }}</th>
                                    <th class="right">{{ rupiah(formatDecimal($total_subtotal)) }}</th>

                                    <th class="right">{{ rupiah(formatDecimal($total_profit)) }}</th>
                                </tr>
                            </table>
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
