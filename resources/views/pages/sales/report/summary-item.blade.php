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
                            <h3 class="report-title">Laporan Penjualan</h3>
                            
                            <table>
                                <tr class="border-cust">
                                    <th class="center">NAMA ITEM</th>
                                    <th class="center">QTY</th>
                                    <th class="center">HARGA</th>
                                    <th>DISKON</th>
                                    <th>SUBTOTAL</th>
                                    <th>DPP</th>
                                    <th>PPN</th>
                                    <th>PPH</th>
                                    <th>GRAND TOTAL</th>
                                </tr>
                                <?php 
                                    $total_subtotal = 0;
                                    $total_dpp      = 0;
                                    $total_ppn      = 0;
                                    $total_pph      = 0;
                                    $total_final    = 0;
                                ?>


                                @forelse($data as $item)
                                    <?php
                                        $total_subtotal += $item->subtotal;
                                        $total_dpp += $item->subtotal_dpp;
                                        $total_ppn += $item->subtotal_ppn;
                                        $total_pph += $item->subtotal_pph;
                                        $total_final += $item->final_subtotal;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ rupiah($item->price) }}</td>
                                        <td>{{ $item->discount }}</td>
                                        <td>{{ rupiah($item->subtotal) }}</td>
                                        <td>{{ rupiah($item->subtotal_dpp) }}</td>
                                        <td>{{ rupiah($item->subtotal_ppn) }}</td>
                                        <td>{{ rupiah($item->subtotal_pph) }}</td>
                                        <td>{{ rupiah($item->final_subtotal) }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="4" class="right">TOTAL</th>
                                    <th>{{ rupiah($total_subtotal) }}</th>
                                    <th>{{ rupiah($total_dpp) }}</th>
                                    <th>{{ rupiah($total_ppn) }}</th>
                                    <th>{{ rupiah($total_pph) }}</th>
                                    <th>{{ rupiah($total_final) }}</th>
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