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
            border: 1px solid #000;
        }

        .table tbody tr td {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .detail-header {
            border: 1px solid #000;
            background: #f0f0f0;
            border-bottom: none;
            font-weight: 800;
        }
    </style>
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
                            <h3 class="report-title">{{ $title }}</h3>
                            <table class="table">
                                @if ($cashflow)
                                    <tbody>
                                        <tr>
                                            <td>Nama Outlet </td>
                                            <td>{{ $outletName }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama Kasir </td>
                                            <td>{{ $userName }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>{{ $label }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Modal Kas</td>
                                            <td>{{ rupiah(formatDecimal($cashflowClose->total_capital)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Penjualan</td>
                                            <td>{{ rupiah(formatDecimal($cashflow->total_amount)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total HPP Penjualan</td>
                                            <td>{{ rupiah(formatDecimal($totalHpp->subtotal_hpp)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Uang Masuk (Real Setoran)</td>
                                            <td>{{ rupiah(formatDecimal($cashflowClose->total_real_amount)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Total Pengeluaran</td>
                                            <td>{{ rupiah(formatDecimal($cashflowClose->total_expense)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Laba Kotor</td>
                                            <td>{{ rupiah(formatDecimal($cashflowClose->total_income - $totalHpp->subtotal_hpp)) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Laba Bersih</td>
                                            <td>{{ rupiah(formatDecimal($cashflowClose->total_income - $totalHpp->subtotal_hpp - $cashflowClose->total_expense)) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                @endif
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
