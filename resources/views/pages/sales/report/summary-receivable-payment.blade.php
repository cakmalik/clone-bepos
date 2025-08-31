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
                            <h3 class="report-title">Laporan Pembayaran Piutang</h3>
                            <br>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">TANGGAL</th>
                                    <th class="center">NO. INV</th>
                                    <th class="center">CABANG</th>
                                    <th class="center">CUSTOMER</th>
                                    <th class="center">PEMBAYARAN</th>
                                    <th class="center">NOMINAL</th>
                                </tr>

                                <?php $total_terbayar = 0; ?>
                                @forelse($data as $value)
                                    <?php $total_terbayar += $value->nominal_payment; ?>

                                    <tr>
                                        <td>{{ dateWithTime($value->payment_date) }}</td>
                                        <td>{{ $value->transaction_code }}</td>
                                        <td>{{ $value->branch_office }}</td>
                                        <td>{{ $value->customer_code.' - '.$value->customer }}</td>
                                        <td>{{ $value->payment_type }}</td>
                                        <td class="right"> {{ rupiah($value->nominal_payment) }}</td>
                                    </tr>
                                @empty
                                    <tr class="border-cust border-lr">
                                        <td class="center" colspan="8">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                                    <tr class="border-cust border-lr">
                                        <td colspan="5" class="right"><b>TOTAL</b></td>
                                        <td class="right"><b>{{ rupiah($total_terbayar) }}</b> </td>
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