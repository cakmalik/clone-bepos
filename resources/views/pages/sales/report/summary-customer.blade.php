<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('nota.style-normal')
</head>
<body>
    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <div class="header-info">
                            @include('nota.kop')
                        </div>
                    </th>


                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">Laporan Penjualan Summary Customer</h3>

                            <table>
                                <tr class="border-cust">
                                    <th class="center">NO INVOICE</th>
                                    <th class="center">TANGGAL</th>
                                    <th class="center">OUTLET</th>
                                    <th class="center">NPWP</th>
                                    <th>PRODUK</th>
                                    <th>QTY</th>
                                    <th>HARGA BELI</th>
                                    <th>SUB HARGA BELI</th>
                                    <th>HARGA JUAL</th>
                                    <th>DISKON</th>
                                    <th>SUBTOTAL</th>
                                    <th>DPP</th>
                                    <th>PPN</th>
                                    <th>PPH</th>
                                    <th>GRAND TOTAL</th>
                                    <th>PEMBAYARAN</th>
                                </tr>
                                <?php 
                                    $total_hpp = 0;
                                    $total_dpp = 0;
                                    $total_ppn = 0;
                                    $total_pph = 0;
                                    $total_final = 0;
                                ?>
                                @forelse($data as $item)
                                    <?php
                                        $total_hpp += $item->subtotal_hpp;
                                        $total_dpp += $item->subtotal_dpp;
                                        $total_ppn += $item->subtotal_ppn;
                                        $total_pph += $item->subtotal_pph;
                                        $total_final += $item->final_subtotal;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->code }}</td>
                                        <td>{{ dateWithTime($item->transaction_date) }}</td>
                                        <td>{{ $item->customer_code.' - '.$item->customer }}</td>
                                        <td class="center">{{ $item->npwp }}</td>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ rupiah($item->buy_price) }}</td>
                                        <td>{{ rupiah($item->subtotal_hpp) }}</td>
                                        <td>{{ rupiah($item->price) }}</td>
                                        <td>{{ $item->discount }}</td>
                                        <td>{{ rupiah($item->subtotal) }}</td>
                                        <td>{{ $item->subtotal_dpp }}</td>
                                        <td>{{ $item->subtotal_ppn }}</td>
                                        <td>{{ $item->subtotal_pph }}</td>
                                        <td>{{ rupiah($item->final_subtotal) }}</td>
                                        <td>{{ $item->payment_method }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="6" class="right">TOTAL</th>
                                    <th>{{ rupiah($total_hpp) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>{{ rupiah($total_dpp) }}</th>
                                    <th>{{ rupiah($total_ppn) }}</th>
                                    <th>{{ rupiah($total_pph) }}</th>
                                    <th>{{ rupiah($total_final) }}</th>
                                    <th>-</th>
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