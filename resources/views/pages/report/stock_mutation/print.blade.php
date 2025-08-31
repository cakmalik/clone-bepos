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
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">Laporan Mutasi Stok</h3>
                            <br><br>
                            <div style="display:flex; flex-direction: row; flex-wrap: wrap; padding: 4px;">
                                <table class="table">
                                    <thead>
                                        <tr class="text-center" style="font-weight: 800;">
                                            <th>TANGGAL</th>
                                            <th>NOMOR DOKUMEN</th>
                                            <th>DARI</th>
                                            <th>MENUJU</th>
                                            <th>KODE</th>
                                            <th>NAMA PRODUK</th>
                                            <th>QTY</th>
                                            <td>STATUS</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mutations->toArray() as $mutation)
                                        @php
                                            $destinationName = $mutation['inventory_destination']['name'] ?? $mutation['outlet_destination']['name'] ?? '-';
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ sizeof($mutation['items']) }}">
                                                {{ dateWithTime($mutation['date']) }}
                                            </td>
                                            <td rowspan="{{ sizeof($mutation['items']) }}">
                                                {{ $mutation['code'] }}
                                            </td>
                                            <td rowspan="{{ sizeof($mutation['items']) }}">
                                                {{ $mutation['inventory_source']['name'] ?? $mutation['outlet_source']['name'] ?? '-' }}
                                            </td>
                                            <td rowspan="{{ sizeof($mutation['items']) }}">
                                                {{ $destinationName }}
                                            </td>
                                            <td>{{ $mutation['items'][0]['product']['code'] }}</td>
                                            <td>{{ $mutation['items'][0]['product']['name'] }}</td>
                                            <td>{{ formatDecimal($mutation['items'][0]['qty']) }}</td>
                                            <td class="text-title">{{ $mutation['status'] }}</td>
                                        </tr>
                                        @foreach (array_slice($mutation['items'], 1) as $item)
                                            <tr>
                                                <td>{{ $item['product']['code'] }}</td>
                                                <td>{{ $item['product']['name'] }}</td>
                                                <td>{{ formatDecimal($item['qty']) }}</td>
                                                <td class="text-title">{{ $mutation['status'] }}</td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data yang ditemukan.</td>
                                        </tr>
                                    @endforelse
                                    
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
