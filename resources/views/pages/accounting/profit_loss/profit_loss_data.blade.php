<?php
$totalSales = 0;
$totalSellingCapital = 0;
$totalOperatingCost = 0;
$totalNonOperatingCost = 0;
$totalNonOperatingIncome = 0;
?>

<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('pages.nota.style-normal')
</head>

<body>
    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <div class="header-info">
                            @include('pages.nota.kop')
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">LABA RUGI</h3>
                            <h3 class="report-title">{{ $periode }}</h3>
                            <br><br>
                            <table class="border-lr border-cust">
                                <tr>
                                    <th>Acc</th>
                                    <th>Keterangan</th>
                                    <th>Nominal</th>
                                </tr>

                                @foreach ($sales as $key => $value)
                                    <tr class="border-cust">
                                        <td class="center">{{ $value->code }}</td>
                                        <td>{{ $value->account_name }}</td>
                                        <td class="right">{{ rupiah($value->total_credit - $value->total_debit) }}</td>
                                        <?php $totalSales += $value->total_credit - $value->total_debit; ?>
                                    </tr>
                                @endforeach
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL PENJUALAN</b></td>
                                    <td class="right"><b>{{ rupiah($totalSales) }}</b></td>
                                </tr>
                                @foreach ($sellingCapital as $value)
                                    <tr>
                                        <td class="center">{{ $value->code }}</td>
                                        <td>{{ $value->account_name }}</td>
                                        <td class="right">{{ rupiah($value->total_debit - $value->total_credit) }}</td>
                                        <?php $totalSellingCapital += $value->total_debit - $value->total_credit; ?>
                                    </tr>
                                @endforeach
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL HARGA BELI</b></td>
                                    <td class="right"><b>{{ rupiah($totalSellingCapital) }}</b></td>
                                </tr>
                                <tr class="border-cust">
                                    <td></td>
                                    <td><b>LABA KOTOR</b></td>
                                    <td></td>
                                </tr>
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL LABA KOTOR</b></td>
                                    <td class="right"><b>{{ rupiah($totalSales - $totalSellingCapital) }}</b></td>
                                </tr>
                                <tr class="">
                                    <td></td>
                                    <td><b>BIAYA OPERASIONAL</b></td>
                                    <td></td>
                                </tr>
                                @foreach ($operatingCost as $value)
                                    <tr>
                                        <td class="center">{{ $value->code }}</td>
                                        <td>{{ $value->account_name }}</td>
                                        <td class="right">{{ rupiah($value->total_debit - $value->total_credit) }}
                                        </td>
                                        <?php $totalOperatingCost += $value->total_debit - $value->total_credit; ?>
                                    </tr>
                                @endforeach
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL BIAYA OPERASIONAL</b></td>
                                    <td class="right"><b>{{ rupiah($totalOperatingCost) }}</b></td>
                                </tr>
                                <tr class="">
                                    <td></td>
                                    <td><b>PENDAPATAN NON OPERASIONAL</b></td>
                                    <td></td>
                                </tr>
                                @foreach ($nonOperatingIncome as $value)
                                    <tr>
                                        <td class="center">{{ $value->code }}</td>
                                        <td>{{ $value->account_name }}</td>
                                        <td class="right">{{ rupiah($value->total_credit - $value->total_debit) }}
                                        </td>
                                        <?php $totalNonOperatingIncome += $value->total_credit - $value->total_debit; ?>
                                    </tr>
                                @endforeach
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL PENDAPATAN NON OPERASIONAL</b></td>
                                    <td class="right"><b>{{ rupiah($totalNonOperatingIncome) }}</b></td>
                                </tr>
                                <tr class="">
                                    <td></td>
                                    <td><b>BIAYA NON OPERASIONAL</b></td>
                                    <td></td>
                                </tr>
                                @foreach ($nonOperatingCost as $value)
                                    <tr>
                                        <td class="center">{{ $value->code }}</td>
                                        <td>{{ $value->account_name }}</td>
                                        <td class="right">{{ rupiah($value->total_debit - $value->total_credit) }}
                                        </td>
                                        <?php $totalNonOperatingCost += $value->total_debit - $value->total_credit; ?>
                                    </tr>
                                @endforeach
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>TOTAL BIAYA NON OPERASIONAL</b></td>
                                    <td class="right"><b>{{ rupiah($totalNonOperatingCost) }}</b></td>
                                </tr>
                                <tr class="border-cust">
                                    <td colspan="2" class="right"><b>LABA RUGI BERSIH</b></td>
                                    <td class="right">
                                        <b>{{ rupiah($totalSales + $totalNonOperatingIncome - $totalSellingCapital - $totalOperatingCost - $totalNonOperatingCost) }}</b>
                                    </td>
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
