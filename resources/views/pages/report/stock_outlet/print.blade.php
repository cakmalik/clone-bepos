<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('pages.nota.style-normal')
</head>

<body>


    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <div class="header-info">
                            <table style="width: 100%; margin-bottom: 5px; border: 0;">
                                <tr style="padding: 0;">
                                    <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                                        @if ($company->image)
                                            <img src="{{ asset('storage/images/' . $company->image) }}" alt=""
                                                width="70" height="70">
                                        @else
                                            <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70"
                                                height="70">
                                        @endif
                                    </td>
                                    <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
                                        <h1 style="text-align: center;">
                                            {{ $company->name }}
                                        </h1>
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->address }}
                                        </p>
                                    
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->about }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </th>


                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">

                            <h3 class="center">Laporan Stok Outlet </h3>
                            <table style="width: 30%; font-weight: bold">
                                <tr>
                                    <td>Outlet</td>
                                    <td>:</td>
                                    <td>{{ $inventory }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal</td>
                                    <td>:</td>
                                    <td>{{ $stockDate }}</td>
                                </tr>
                            </table>
                           
                            <br>
                            <table id="nota_journal_table">
                                <thead>
                                    <tr class="border-cust">
                                        <th>Barcode</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stock as $value)
                                        <tr class="border-cust border-lr">
                                            <td class="center">{{ $value->barcode }}</td>
                                            <td>{{ $value->product_name }}</td>
                                            <td>{{ $value->product_category }}</td>
                                            <td>{{ $value->stock_current }}</td>
                                            <td class="center">{{ $value->product_unit }}</td>

                                        </tr>
                                    @endforeach
                                </tbody>
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
