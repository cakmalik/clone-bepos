<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('layouts.nota.style-normal')
</head>

<body>
    <div class="print">
        <table class="report-container">
            <thead class="report-header">
                <tr>
                    <th class="report-header-cell">
                        <h2>STOK MUTASI</h2>
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
                                        <td
                                            style="width: 45%; border-bottom: 1px solid #000; padding: 5px;text-align: left;">
                                            <table style="width: 100%; border: 0;">
                                                <tbody>
                                                    <tr>
                                                        <td style="width: 25%;text-align: left;">
                                                            No.Mutasi
                                                        </td>
                                                        <td style="width: 5%;text-align: left;">:</td>
                                                        <td style="width: 75%;text-align: left;">
                                                            {{ $mutation->code }}
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>Tanggal</td>
                                                        <td>:</td>
                                                        <td>
                                                            {{ date('d/m/Y H:i', strtotime($mutation->date)) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Dari</td>
                                                        <td>:</td>
                                                        <td>
                                                            {{ $source->name ?? '-' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Ke</td>
                                                        <td>:</td>
                                                        <td>
                                                            {{ $destination->name ?? '-' }}
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
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
                            <br>
                            <table style="border-collapse: collapse; border-bottom: 1px solid #000;">
                                <thead>
                                    <tr>
                                        <th style="border: 1px solid #000;">KODE MUTASI</th>
                                        <th style="border: 1px solid #000;">NAMA PRODUK</th>
                                        <th style="border: 1px solid #000;">QTY</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mutation->items as $item)
                                        <tr style="border-right: 1px solid #000; border-left: 1px solid #000;">
                                            <td style="text-align: center;">
                                                {{ $mutation->code }}-{{ $item->product->code }}</td>
                                            <td style="text-align: center;">{{ $item->product->name }}</td>
                                            <td style="text-align: center;">{{ $item->qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <br>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot class="report-footer">
                <tr>
                    <td class="report-footer-cell">
                        <div class="footer-info">
                            <!-- footer -->
                            <table>
                                <tbody>
                                    <tr>
                                        <th class="right" colspan="2" style="width: 50%;">&nbsp;
                                        </th>
                                        <td class="center border-cust">
                                            Mengetahui
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
                                        </td>
                                        <td class="center border-cust">
                                            Diterima
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
                                        </td>
                                        <td class="center border-cust">
                                            Dibuat
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            {{ $username }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- footer -->
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        
    </div>
    <script>
        window.onload = function() {
            parent.iframeLoaded();
        }
    </script>
</body>

</html>
