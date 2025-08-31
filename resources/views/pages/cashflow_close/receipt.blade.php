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

        .table tbody tr td,
        .table thead tr th {
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
                        <h2></h2>
                        <br />
                        <div class="header-info">
                            <table style="width: 100%; margin-bottom: 5px; border: 0;">
                                <tbody>
                                    <tr style="padding: 0;">
                                        <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                                            <img src="{{ asset('storage/images/' . profileCompany()->image) }}"
                                                alt="" width="70" height="70"
                                                onerror="this.src='{{ asset('img/default_img.jpg') }}'; this.onerror=null;">
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
                            <div style="display: flex; font-weight: 800; font-size: 14px; margin-top: 12px; margin-bottom: 5px;">
                                <div>
                                    CASHFLOW CLOSE
                                </div>
                            </div>

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td width="50%">Tanggal</td>
                                        <td>{{ dateWithTime($cashflowClose->date) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Ditutup Oleh</td>
                                        <td>{{ $cashflowClose->user?->users_name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Modal</td>
                                        <td>{{ rupiah($cashflowClose->capital_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pemasukan</td>
                                        <td>{{ rupiah($cashflowClose->income_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pengeluaran</td>
                                        <td>{{ rupiah($cashflowClose->expense_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total</td>
                                        <td>{{ rupiah($cashflowClose->capital_amount + $cashflowClose->income_amount - $cashflowClose->expense_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Selisih</td>
                                        <td>{{ rupiah($cashflowClose->difference) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Nominal Asli</td>
                                        <td>{{ rupiah($cashflowClose->real_amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="display: flex; font-weight: 800; font-size: 14px; margin-top: 12px; margin-bottom: 5px;">
                                <div>
                                    RINCIAN PEMBAYARAN
                                </div>
                            </div>
                            <table class="table">
                                <tbody>
                                    @foreach ($paymentSums as $paymentMethod => $total)
                                    <tr>
                                        <td width="50%">Pembayaran {{ $paymentMethod }}</td>
                                        <td>{{ rupiah($total) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 12px;">
                                <thead>
                                    <tr>
                                        <th class="text-center">KODE</th>
                                        <th>TANGGAL/WAKTU</th>
                                        <th>KETERANGAN</th>
                                        <th class="text-center">TIPE</th>
                                        <th class="text-center" width="12%">METODE BAYAR</th>
                                        <th class="text-right">NOMINAL</th>
                                        <th class="text-right">PROFIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalAmount = 0;
                                        $totalProfit = 0;
                                    @endphp
                                    @foreach ($cashflowClose->cashflows as $cashflow)
                                        <tr>
                                            <td>{{ $cashflow->code ?? '-' }}</td>
                                            <td>{{ dateWithTime($cashflow->created_at) }}</td>
                                            <td>{{ $cashflow->desc }}</td>
                                            <td style="text-transform: uppercase;">{{ $cashflow->type }}</td>
                                            <td>{{ $cashflow->transaction->paymentMethod->name ?? '-' }}</td>
                                            <td class="text-right">{{ rupiah($cashflow->amount) }}</td>
                                            <td class="text-right">{{ rupiah($cashflow->profit) }}</td>
                                        </tr>
                                        @php

                                            if($cashflow->type == 'out') {
                                                $totalAmount -= $cashflow->amount;
                                            } else {
                                                $totalAmount += $cashflow->amount;
                                            }
                                            $totalProfit += $cashflow->profit;
                                        @endphp

                                    @endforeach
                                    <tr style="font-weight: 800; border-top: 1px solid #000;">
                                        <td colspan="5" class="text-right">
                                            Total
                                        </td>
                                        <td class="text-right">
                                            {{ rupiah($totalAmount) }}
                                        </td>
                                        <td class="text-right">{{ rupiah($totalProfit) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
<script>
    window.onload = function() {
        parent.iframeLoaded();
    }
</script>

</html>
