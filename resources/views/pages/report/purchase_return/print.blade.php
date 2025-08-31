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
                            <h3 class="report-title">Laporan Retur Pembelian</h3>
                            <br><br>
                            @if ($summary_detail)
                                @forelse($orders as $order)
                                    <div>
                                        <table class="detail-header">
                                            <tbody>
                                                <tr>
                                                    <td>No. Invoice</td>
                                                    <td>:</td>
                                                    <td>{{ $order['purchase_invoice']['code'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Tanggal</td>
                                                    <td>:</td>
                                                    <td>{{ dateWithTime($order['purchase_date']) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Supplier</td>
                                                    <td>:</td>
                                                    <td>{{ $order['supplier']['name'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="table">
                                            <thead>
                                                <tr style="text-align: center;">
                                                    <th>NAMA ITEM</th>
                                                    <th>QTY</th>
                                                    <th>HARGA BELI</th>
                                                    <th>SUBTOTAL</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order['purchase_detail_retur'] as $item)
                                                    <tr>
                                                        <td>{{ $item['product_name'] }}</td>
                                                        <td class="text-center">{{ floatval($item['qty']) }}</td>
                                                        <td class="text-right">{{ rupiah($item['price']) }}</td>
                                                        <td class="text-right">{{ rupiah($item['subtotal']) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr style="font-weight: 800; border-top: 1px solid #000;"
                                                    class="text-right">
                                                    <td colspan="3">TOTAL</td>
                                                    <td>{{ rupiah($totalNominalRetur) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @empty
                                    <div>
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endforelse
                            @else
                                <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>TANGGAL</th>
                                                <th>INVOICE</th>
                                                <th>NOMOR RETUR</th>
                                                <th>SUPPLIER</th>
                                                <th>NOMINAL TAGIHAN</th>
                                                <th>NOMINAL RETUR</th>
                                                <th>DIBAYAR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                <tr>
                                                    <td>{{ dateWithTime($order['purchase_date']) }}</td>
                                                    <td>{{ $order['purchase_invoice']['code'] }}</td>
                                                    <td>{{ $order['code'] }}</td>
                                                    <td>{{ $order['supplier']['name'] }}</td>
                                                    <td class="text-right">
                                                        {{ currency($order['purchase_invoice']['total_invoice']) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ currency($order['purchase_invoice']['nominal_returned']) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ currency($order['purchase_invoice']['nominal_paid']) }}</td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 6 + sizeof($methods) }}" class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endforelse
                                            <tr style="border-top: 1px solid #000;">
                                                <td colspan="4" style="text-align: center; font-weight: 800;">
                                                    Total
                                                </td>
                                                <td style="text-align: right; font-weight: 800;">
                                                    {{ rupiah($totalNominalInvoice) }}
                                                </td>
                                                <td style="text-align: right; font-weight: 800;">
                                                    {{ rupiah($totalNominalRetur) }}
                                                </td>
                                                <td style="text-align: right; font-weight: 800;">
                                                    {{ rupiah($totalPaid) }}
                                                </td>
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
    <script>
        window.onload = function() {
            parent.iframeLoaded();
        }
    </script>
</body>

</html>
