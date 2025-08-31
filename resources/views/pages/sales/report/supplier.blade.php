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
                            <h3 class="report-title">Laporan Penjualan By Supplier ({{ $data[1] }})</h3>

                            <table>
                                <tr class="border-cust">
                                    <th class="center">Supplier</th>
                                    <th class="center">Produk</th>
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
                                $supplierName = '-';
                                ?>
                                

                                @forelse($data[0] as $item)
                                    <?php

                                    $qty = formatDecimal($item->qty);    
                                    $subtotal_hpp = number_format($item->subtotal_hpp, 0, ',', '.');
                                    $subtotal = number_format($item->subtotal, 0, ',', '.');
                                    $final_profit = number_format($item->final_profit, 0, ',', '.');

                                    $total_qty += $item->qty;
                                    $total_subtotal_hpp += $item->subtotal_hpp;
                                    $total_subtotal += $item->subtotal;
                                    $total_profit += $item->final_profit;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $supplierName == $item->supplier_name ? '' : $item->supplier_name }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td class="center">{{ $qty }}</td>
                                        <td class="right">{{ rupiah($subtotal_hpp) }}</td>
                                        <td class="right">{{ rupiah($subtotal) }}</td>
                                        <td class="right">{{ rupiah($final_profit) }}</td>
                                    </tr>

                                    <?php $supplierName =  $item->supplier_name; ?>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="2" class="center">TOTAL</th>
                                    <th>{{ $total_qty }}</th>
                                    <th class="right">{{ rupiah($total_subtotal_hpp) }}</th>
                                    <th class="right">{{ rupiah($total_subtotal) }}</th>

                                    <th class="right">{{ rupiah($total_profit) }}</th>
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
