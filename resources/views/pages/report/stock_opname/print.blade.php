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

        .text-title:first-letter {
            text-transform: capitalize;
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
                <tr class="report-content-cell">
                    <td>
                        <div class="main">
                            <h3 class="report-title">Laporan Stok Opname</h3>
                            <br><br>
                            <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                <table class="table">
                                    <thead>
                                        <tr class="text-center" style="font-weight: 800;">
                                            <th>TANGGAL</th>
                                            <th>NO. SO</th>
                                            <th>GUDANG</th>
                                            <th>BARCODE</th>
                                            <th>NAMA BARANG</th>
                                            <th>KATEGORI</th>
                                            <th>QTY SYSTEM</th>
                                            <th>QTY SO</th>
                                            <th>SELISIH</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($opnames->toArray() as $opname)
                                            <tr>
                                                <td rowspan="{{ sizeof($opname['stock_opname_details']) }}">
                                                    {{ dateWithTime($opname['so_date']) }}</td>
                                                <td rowspan="{{ sizeof($opname['stock_opname_details']) }}">
                                                    {{ $opname['code'] }}</td>
                                                @if ($opname['inventory_id'])
                                                    <td rowspan="{{ sizeof($opname['stock_opname_details']) }}">
                                                        {{ $opname['inventory']['name'] }}</td>
                                                @else
                                                    <td rowspan="{{ sizeof($opname['stock_opname_details']) }}">
                                                        {{ $opname['outlet']['name'] }}</td>
                                                @endif

                                                <td>{{ $opname['stock_opname_details'][0]['product']['barcode'] }}</td>
                                                <td>{{ $opname['stock_opname_details'][0]['product']['name'] }}</td>
                                                <td>{{ $opname['stock_opname_details'][0]['product']['product_category']['name'] }}
                                                </td>
                                                <td>{{ formatDecimal($opname['stock_opname_details'][0]['qty_system']) }}</td>
                                                <td>{{ formatDecimal($opname['stock_opname_details'][0]['qty_so']) }}</td>
                                                <td>{{ formatDecimal($opname['stock_opname_details'][0]['qty_selisih']) }}</td>
                                            </tr>
                                            @foreach (array_slice($opname['stock_opname_details'], 1) as $item)
                                                <tr>
                                                    <td>{{ $item['product']['barcode'] ?? '-' }}</td>
                                                    <td>{{ $item['product']['name'] ?? '-' }}</td>
                                                    <td>{{ $item['product']['product_category']['name'] ?? '-' }}</td>
                                                    <td>{{ formatDecimal($item['qty_system']) }}</td>
                                                    <td>{{ formatDecimal($item['qty_so']) }}</td>
                                                    <td>{{ formatDecimal($item['qty_selisih']) }}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    Data Tidak Ditemukan
                                                </td>
                                            </tr>
                                        @endforelse
                                        {{-- @forelse($opnames as $opname)
                                            <?php $row_span = $opname->stockOpnameDetails->count(); ?>
                                            <tr>
                                                <td rowspan="{{ $row_span }}">{{ dateWithtime($opname->so_date) }}</td>
                                                <td rowspan="{{ $row_span }}">
                                                    {{ $opname->code }}</td>
                                                @if ($opname->inventory_id)
                                                    <td rowspan="{{ $row_span }}">
                                                        {{ $opname->inventory?->name }}</td>
                                                @else
                                                    <td rowspan="{{ $row_span }}">
                                                        {{ $opname->outlet?->name }}</td>
                                                @endif
                                                <td>{{ $opname->stockOpnameDetails?->first()?->product?->code }}</td>
                                                <td>{{ $opname->stockOpnameDetails?->first()?->product?->name }}</td>
                                                <td>{{ $opname->stockOpnameDetails?->first()?->product?->productCategory?->name }} </td>
                                                <td>{{ $opname->stockOpnameDetails?->first()?->qty_system }}</td>
                                                <td>{{ $opname->stockOpnameDetails?->first()?->qty_so }}</td>
                                                <td>{{ $opname->stockOpnameDetails?->first()?->qty_selisih }}</td>
                                            </tr>
                                            @foreach ($opname->stockOpnameDetails as $item)
                                                <tr>
                                                    <td>{{ $item->product?->code ?? '-' }}</td>
                                                    <td>{{ $item->product?->name ?? '-' }}</td>
                                                    <td>{{ $item->product?->product_category?->name ?? '-' }}</td>
                                                    <td>{{ $item->qty_system }}</td>
                                                    <td>{{ $item->qty_so }}</td>
                                                    <td>{{ $item->qty_selisih }}</td>
                                                </tr>
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    Data Tidak Ditemukan
                                                </td>
                                            </tr>
                                        @endforelse --}}
                                    </tbody>
                                </table>
                            </div>
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
