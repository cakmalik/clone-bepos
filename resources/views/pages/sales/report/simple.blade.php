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
                            <h3 class="report-title">Laporan Penjualan Sederhana</h3>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">#</th>
                                    <th class="center">NO INVOICE</th>
                                    <th class="center">TGL</th>
                                    <th class="center">CUSTOMER</th>
                                    <th>SALES</th>

                                    <th>PEMBAYARAN</th>
                                    <th>SUB TOTAL</th>
                                </tr>
                                <?php
                                $payment_totals = [];
                                $total_sub = 0;
                                ?>
                                @forelse($data as $item)
                                    <?php
                                    $total_sub += $item->final_amount;

                                    if (array_key_exists($item->payment_method, $payment_totals)) {
                                        $payment_totals[$item->payment_method] += 1;
                                    } else {
                                        $payment_totals[$item->payment_method] = 0;
                                    }
                                    ?>

                                    <tr class="border-lr">
                                        <td style="width: 6px">{{ $loop->iteration }}</td>
                                        <td>{{ $item->sale_code }}</td>
                                        <td style="text-align: center">{{ dateWithTime($item->sale_date) }}</td>
                                        <td>{{ $item->customer_code . ' - ' . $item->customer }}</td>
                                        <td style="text-align: center">{{ $item->user }}</td>

                                        <td style="text-align: center; text-transform: uppercase">
                                            {{ $item->payment_method }}</td>
                                        <td style="text-align: right">{{ rupiah($item->final_amount) }}</td>
                                    </tr>

                                @empty
                                @endforelse
                                <tr class="border-cust">
                                    <th colspan="3" class="left" style="font-size: 16px">TOTAL</th>
                                    <td colspan="3" class="center">
                                        @foreach ($payment_totals as $key => $pm)
                                            {{ $key . ':' . $pm }} {{ $loop->last ? '' : '|' }}
                                        @endforeach
                                    </td>

                                    <th class="right" style="font-size: 16px">{{ rupiah($total_sub) }} </th>

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
