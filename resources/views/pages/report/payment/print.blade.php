<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('layouts.nota.style-normal')
    <style>
        .table,
        .table thead tr,
        .table thead th {
            border-collapse: collapse;
            border: 1px solid black;
        }

        .table tbody tr td {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <h2></h2>
                        <br />
                        <div class="header-info">
                            <table style="width: 100%; margin-bottom: 5px; border: 0;">
                                <tbody>
                                    <tr style="padding: 0;">
                                        <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                                            <img src="{{ asset('storage/images/' . profileCompany()->image) }}"
                                                alt="" width="70" height="70">
                                        </td>
                                        <td style="width: 45%; border-bottom: 1px solid #000; padding: 5px;">
                                            <h3 style="text-align: left;">
                                                {{ profileCompany()->name }}
                                            </h3>
                                            <p style="padding: 0;text-align: left;">
                                                {{ profileCompany()->address }}
                                                {{ profileCompany()->email . ' ' . profileCompany()->phone }}
                                            </p>

                                            <p style="padding: 0;text-align: left;">
                                                <span>a.n {{ profileCompany()->name }}</span>
                                            </p>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">Laporan Pembayaran</h3>
                            <br><br>
                            <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>TANGGAL</th>
                                            <th>KASIR</th>
                                            <th>NO. TRANSAKSI</th>
                                            <th>CUSTOMER</th>
                                            <th>TIPE PEMBAYARAN</th>
                                            <th>NOMINAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sales as $sale)
                                            <tr>
                                                <td>{{ dateWithTime($sale->sale_date) }}</td>
                                                <td>{{ $sale->cashier->users_name }}</td>
                                                <td>{{ $sale->sale_code }}</td>
                                                <td>{{ $sale->customer->name }}</td>
                                                <td>{{ $sale->paymentMethod->name }}</td>
                                                <td style="text-align: right;">{{ currency($sale->final_amount) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    Data Tidak Ditemukan
                                                </td>
                                            </tr>
                                        @endforelse
                                        <tr style="border-top: 1px solid black;">
                                            <td colspan="5" style="text-align: right; font-weight: 800;">
                                                Total
                                            </td>
                                            <td style="text-align: right; font-weight: 800;">
                                                {{ rupiah($total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="2">SUMMARY</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($paymentMethod as $method=>$value)
                                            <tr>
                                                <td>{{ $method }}</td>
                                                <td style="text-align: right;">{{ rupiah($value) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    Data Tidak Ditemukan
                                                </td>
                                            </tr>
                                        @endforelse
                                        <tr style="border-top: 1px solid black;">
                                            <td style="text-align: center; font-weight: 800;">
                                                Total
                                            </td>
                                            <td style="text-align: right; font-weight: 800;">
                                                {{ rupiah($total) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br><br>
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
