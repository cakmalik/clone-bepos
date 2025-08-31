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
                            <h3 class="report-title">Laporan Penjualan Summary User
                                ({{ $data['start_date'].' - '.$data['end_date'] }})
                            </h3>

                            <table>
                                <tr class="border-cust">
                                    <th class="center">USER</th>
                                    <th>Modal</th>
                                    <th>Penjualan System</th>
                                    <th>Real count</th>
                                    <th>Selisih</th>
                                </tr>
                                <?php
                                $total_subtotal = 0;
                                $total_count = 0;
                                $total_different = 0;
                                $total_capital = 0;
                                ?>
                                @forelse($data['summary'] as $item)
                                    <?php
                                    
                                    $income_amount = number_format($item->income_amount, 0, ',', '.');
                                    $real_amount = number_format($item->real_amount, 0, ',', '.');
                                    $difference = number_format($item->difference, 0, ',', '.');
                                    $capital_amount = number_format($item->capital_amount, 0, ',', '.');

                                    $total_subtotal += $item->income_amount;
                                    $total_count += $item->real_amount;
                                    $total_different += $item->difference;
                                    $total_capital += $item->capital_amount;
                                    ?>

                                    <tr class="border-lr">
                                        <td>{{ $item->users_name }}</td>
                                        <td>{{ rupiah($capital_amount) }}</td>
                                        <td>{{ rupiah($income_amount) }}</td>
                                        <td>{{ rupiah($real_amount) }}</td>
                                        <td>{{ rupiah($difference) }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td colspan="16" class="center">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-cust">
                                    <th style="vertical-align: middle;">TOTAL</th>
                                    <th>{{ rupiah($total_capital) }}</th>
                                    <th>{{ rupiah($total_subtotal) }}</th>
                                    <th>{{ rupiah($total_count) }}</th>
                                    <th>{{ rupiah($total_different) }}</th>
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
