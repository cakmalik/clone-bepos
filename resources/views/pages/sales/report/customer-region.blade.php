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
                            <h3 class="report-title">Laporan Penjualan by Wilayah Customer ({{ $data[1] }})</h3>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">DESA</th>
                                    <th class="center">KECAMATAN</th>
                                    <th class="center">KOTA/KABUPATEN</th>
                                    <th>Jml Customer</th>
                                    <th>TRANSAKSI</th>
                                    <th>TOTAL NOMINAL TRANSAKSI</th>
                                  
                                </tr>
                                <?php
                                    $total_customer = 0;
                                    $total_trx = 0;
                                    $total_nominal = 0;
                                ?>
                                @forelse($data[0] as $item)
                                    <?php
                                        $total_trx += $item->sales_count;
                                        $total_nominal += $item->sales_total;
                                        $total_customer += $item->customer_count;
                                    
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->village_name ?? '-' }}</td>
                                        <td>{{ $item->district_name ?? '-' }}</td>

                                        <td>{{ $item->city_name ?? '-' }}</td>
                                        <td class="center">{{ $item->customer_count == 0 ?
                                            '-' : $item->customer_count }}
                                        </td>
                                        <td class="center">{{ $item->sales_count }}</td>
                                        <td>{{ rupiah($item->sales_total) }}</td>
                                    </tr>
                                @empty
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="3" class="left" style="font-size: 16px">TOTAL</th>
                                    <th  class="center">{{ $total_customer }}</th>
                                    <th  class="center">{{ $total_trx }}</th>
                                    <th >{{ rupiah($total_nominal) }}</th>
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
