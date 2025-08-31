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
                            <h3 class="report-title">Laporan Penjualan by Customer</h3>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">NO INVOICE</th>
                                    <th class="center">TGL</th>
                                    <th class="center">CUSTOMER</th>
                                    <th>SALES</th>

                                    <th>PRODUK</th>
                                    <th>QTY</th>
                                    <th>HARGA JUAL</th>

                                    <th>DISKON</th>
                                    <th>HARGA FINAL</th>

                                    <th>SUB TOTAL</th>
                                    <th>PEMBAYARAN</th>
                                </tr>
                                <?php
                                $total_sub = 0;
                                $_final_price = 0;
                                $_price = 0;
                                $_discount = 0;
                                $_qty = 0;
                                
                                ?>
                                @forelse($data as $item)
                                    <?php

                                    $subtotal = number_format($item->subtotal, 0, ',', '.');
                                    $price = number_format($item->price, 0, ',', '.');
                                    $discount = number_format($item->discount, 0, ',', '.');
                                    $final_price = number_format($item->final_price, 0, ',', '.');

                                    $total_sub += $item->subtotal;
                                    $_price += $item->price;
                                    $_final_price += $item->final_price;
                                    $_discount += $item->discount;
                                    $_qty += $item->qty;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->sale_code }}</td>
                                        <td>{{ dateWithTime($item->sale_date) }}</td>
                                        <td>{{ $item->customer_code . ' - ' . $item->customer }}</td>
                                        <td>{{ $item->user }}</td>

                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ formatDecimal($item->qty) }}</td>
                                        <td>{{ rupiah($price) }}</td>

                                        <td>{{ $discount }}</td>
                                        <td>{{ rupiah($final_price) }}</td>

                                        <td>{{ rupiah($subtotal) }}</td>
                                        <td>{{ $item->payment_method }}</td>
                                    </tr>
                                @empty
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="5" class="left" style="font-size: 16px">TOTAL</th>
                                    <th colspan="1">{{ formatDecimal($_qty) }}</th>
                                    <th colspan="1">{{ rupiah($_price) }}</th>
                                    <th colspan="1">{{ rupiah($_discount) }}</th>
                                    <th colspan="1">{{ rupiah($_final_price) }}</th>
                                    <th colspan="2" class="right" style="font-size: 16px">{{ rupiah($total_sub) }}
                                    </th>
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
