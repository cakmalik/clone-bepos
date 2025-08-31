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
                            <h3 class="report-title">Laporan Penjualan Summary User</h3>

                            <table>
                                <tr class="border-cust">
                                    <th class="center">NO INVOICE</th>
                                    <th class="center">TANGGAL</th>
                                    <th class="center">USER</th>
                                    <th>PRODUK</th>
                                    <th>QTY</th>
                                    <th>HARGA BELI</th>
                                    <th>HARGA JUAL</th>
                                    <th>DISKON</th>
                                    <th>SUBTOTAL</th>
                                    <th>PROFIT</th>
                                    <th>GRAND TOTAL</th>
                                </tr>
                                <?php
                                $total_qty = 0;
                                $total_buy_price = 0;
                                $total_price = 0;
                                $total_discount = 0;
                                $total_subtotal = 0;
                                $total_profit = 0;
                                $total_final_subtotal = 0;
                                ?>
                                @forelse($data as $item)
                                    <?php
                                    $total_qty += $item->qty;
                                    $total_buy_price += $item->buy_price;
                                    $total_price += $item->price;
                                    $total_discount += $item->discount;
                                    $total_subtotal += $item->subtotal;
                                    $total_profit += $item->profit;
                                    $total_final_subtotal += $item->final_subtotal;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->code }}</td>
                                        <td>{{ dateWithTime($item->transaction_date) }}</td>
                                        <td>{{ $item->user_name }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ rupiah($item->buy_price) }}</td>
                                        <td>{{ rupiah($item->price) }}</td>
                                        <td>{{ $item->discount }}</td>
                                        <td>{{ rupiah($item->subtotal) }}</td>
                                        <td>{{ rupiah($item->profit) }}</td>
                                        <td>{{ rupiah($item->final_subtotal) }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="4" class="left" style="vertical-align: middle;">TOTAL</th>
                                    <th>{{ $total_qty }}</th>
                                    <th>{{ rupiah($total_buy_price) }}</th>
                                    <th>{{ rupiah($total_price) }}</th>
                                    <th>{{ rupiah($total_discount) }}</th>
                                    <th>{{ rupiah($total_subtotal) }}</th>
                                    <th>{{ rupiah($total_profit) }}</th>
                                    <th>{{ rupiah($total_final_subtotal) }}</th>
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
