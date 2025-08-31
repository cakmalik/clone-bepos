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
                            <h3 class="report-title">Laporan Penerimaan Pembelian</h3>
                            <br><br>
                            @if ($summary_detail)
                                <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                    <table class="table">
                                        <thead>
                                            <tr class="text-center" style="font-weight: 800;">
                                                <th>TANGGAL</th>
                                                <th>ID REF PENERIMAAN</th>
                                                <th>REF PENERIMAAN</th>
                                                <th>NOMOR PO</th>
                                                <th>SUPPLIER</th>
                                                <th>KODE</th>
                                                <th>NAMA BARANG</th>
                                                <th>DITERIMA</th>
                                                <th>KETERANGAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($receipts as $receipt)
                                                <tr>
                                                    @if ($receipt->rowspan)
                                                        <td rowspan="{{ $receipt->rowspan }}">
                                                            {{ dateWithTime($receipt->received_date) }}</td>
                                                        <td rowspan="{{ $receipt->rowspan }}">
                                                            {{ $receipt->reception_ref_code }}</td>
                                                        <td rowspan="{{ $receipt->rowspan }}">
                                                            {{ $receipt->shipment_ref_code }}</td>
                                                        <td rowspan="{{ $receipt->rowspan }}">
                                                            {{ $receipt->purchase_order_code }}</td>
                                                        <td rowspan="{{ $receipt->rowspan }}">
                                                            {{ $receipt->supplier_name }}</td>
                                                    @endif
                                                    <td>{{ $receipt->product_code }}</td>
                                                    <td>{{ $receipt->product_name }}</td>
                                                    <td>{{ floatval($receipt->accepted_qty) }}</td>
                                                    <td>{{ $receipt->is_bonus ? 'BONUS' : 'PEMBELIAN' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>SUPPLIER</th>
                                                <th>KODE</th>
                                                <th>NAMA BARANG</th>
                                                <th colspan="2">QTY DITERIMA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($suppliers as $key=>$supplier)
                                                @foreach ($supplier['products'] as $code => $products)
                                                    <tr>
                                                        @if ($loop->index == 0)
                                                            <td rowspan="{{ sizeof($supplier['products']) }}">
                                                                {{ $supplier['name'] }}</td>
                                                        @endif
                                                        <td>{{ $code }}</td>
                                                        <td>{{ $products_code[$code] }}</td>
                                                        <td class="center">
                                                            {{ formatDecimal(collect($products)->sum('qty')) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        Data Tidak Ditemukan
                                                    </td>
                                                </tr>
                                            @endforelse
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
