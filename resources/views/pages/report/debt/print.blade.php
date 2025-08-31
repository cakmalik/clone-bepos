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
                            <h3 class="report-title">Laporan Hutang</h3>
                            <br><br>
                            @if ($summary_detail)
                                <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-center" style="font-weight: 800;">
                                                <th>ID</th>
                                                <th>NO.INV.SUPPLIER</th>
                                                <th>TANGGAL INPUT</th>
                                                <th>TANGGAL INVOICE</th>
                                                <th>SUPPLIER</th>
                                                <th>NOMINAL INVOICE</th>
                                                <th>NOMINAL RETUR</th>
                                                <th>UTANG</th>
                                                <th>DIBAYAR</th>
                                                <th>SISA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($invoices as $invoice)
                                                <tr>
                                                    <td>{{ $invoice->code }}</td>
                                                    <td>{{ $invoice->invoice_number }}</td>
                                                    <td>{{ dateWithTime($invoice->created_at) }}</td>
                                                    <td>{{ dateWithTime($invoice->invoice_date) }}</td>
                                                    <td>{{ $invoice->supplier_name }}</td>
                                                    <td class="text-right">{{ rupiah($invoice->nominal) }}</td>
                                                    <td class="text-right">{{ rupiah($invoice->nominal_returned) }}
                                                    </td>
                                                    <td class="text-right">{{ rupiah($invoice->nominal) }}</td>
                                                    <td class="text-right">{{ rupiah($invoice->nominal_paid) }}</td>
                                                    <td class="text-right">
                                                        {{ rupiah($invoice->nominal - $invoice->nominal_paid) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr class="text-right"
                                                style="font-weight: bold; border-top: 1px solid #000;">
                                                <td colspan="5">TOTAL</td>
                                                <td>{{ rupiah($totalNominal) }}</td>
                                                <td>{{ rupiah($totalReturned) }}</td>
                                                <td>{{ rupiah($totalDebt) }}</td>
                                                <td>{{ rupiah($totalPaid) }}</td>
                                                <td>{{ rupiah($totalRemain) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-center" style="font-weight: 800;">
                                                <th>SUPPLIER</th>
                                                <th>NOMINAL HUTANG</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($debts as $debt)
                                                <tr>
                                                    <td>{{ $debt->supplier_name }}</td>
                                                    <td class="text-right">{{ rupiah($debt->nominal_unpaid) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr class="text-right"
                                                style="font-weight: 800; border-top: 1px solid #000;">
                                                <td>TOTAL</td>
                                                <td>{{ rupiah(collect($debts)->sum('nominal_unpaid')) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
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
