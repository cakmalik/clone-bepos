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
                            <h3 class="report-title">Laporan Piutang</h3>
                            <br>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">TANGGAL TRANSAKSI</th>
                                    <th class="center">JATUH TEMPO</th>
                                    <th class="center">KODE TRANSAKSI</th>
                                    <th class="center">CABANG</th>
                                    <th class="center">CUSTOMER</th>
                                    <th class="center">NOMINAL PIUTANG</th>
                                    <th class="center">DIBAYAR</th>
                                    <th class="center">KET</th>
                                </tr>

                                <?php $total_piutang = 0; $total_terbayar=0; ?>
                                @forelse($data as $value)
                                <?php $total_piutang += $value->nominal_payment; $total_terbayar += $value->debt_payment ?>

                                    <tr class="border-lr">
                                        <td>{{ dateWithTime($value->transaction_date) }}</td>
                                        <td>{{ dateWithTime($value->due_date) }}</td>
                                        <td>{{ $value->transaction_code }}</td>
                                        <td>{{ $value->branch_office }}</td>
                                        <td>{{ $value->customer_code.' - '.$value->customer }}</td>
                                        <td class="right"> {{ rupiah($value->nominal_payment) }}</td>
                                        <td class="right"> {{ rupiah($value->debt_payment) }}</td>
                                        <td>Piutang</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust border-lr">
                                        <td class="center" colspan="8">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                                    <tr class="border-cust border-lr">
                                        <td colspan="5" class="right"><b>TOTAL</b></td>
                                        <td class="right"><b>{{ rupiah($total_piutang) }}</b> </td>
                                        <td class="right"><b>{{ rupiah($total_terbayar) }}</b> </td>
                                        <td></td>
                                    </tr>
                              
                            </table>
                            <br>
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