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
                            <div style="display: flex; font-weight: 800; font-size: 14px;">
                                <div>
                                    BUKTI KAS KELUAR
                                </div>
                                <div style="margin-left: auto;">
                                    {{ $proof->code }}
                                </div>
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Telah Terima Dari : {{ $proof->received_from }}</td>
                                        <td>Tanggal : {{ dateWithTime($proof->date) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 12px;">
                                <thead>
                                    <tr>
                                        <th class="text-center">KETERANGAN</th>
                                        <th class="text-center">KODE REK (K)</th>
                                        <th class="text-right">JUMLAH</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($proof->items as $item)
                                        <tr>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->cashMaster->code }} - {{ $item->cashMaster->name }}</td>
                                            <td class="text-right">{{ rupiah($item->nominal) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="font-weight: 800; border-top: 1px solid #000;">
                                        <td colspan="2" class="text-right">
                                            Total
                                        </td>
                                        <td class="text-right">{{ rupiah($total) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div style="margin-top: 24px;">
                                Dengan Huruf :
                            </div>
                            <table class="table" style="margin-top: 12px;">
                                <tbody>
                                    <tr>
                                        <td>{{ terbilang($total) }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table" style="margin-top: 12px;">
                                <tbody>
                                    <tr>
                                        <td class="text-center">Kas Besar</td>
                                        <td class="text-center">Penerima</td>
                                        <td class="text-center">Mengetahui</td>
                                        <td class="text-center">Akuntansi</td>
                                    </tr>
                                    <tr>
                                        <td style="height: 75px;"></td>
                                        <td style="height: 75px;"></td>
                                        <td style="height: 75px;"></td>
                                        <td style="height: 75px;"></td>
                                    </tr>
                                    <tr>
                                        <td>Nama :</td>
                                        <td>Nama :</td>
                                        <td>Nama :</td>
                                        <td>Nama :</td>
                                    </tr>
                                    <tr>
                                        <td>Tgl :</td>
                                        <td>Tgl :</td>
                                        <td>Tgl :</td>
                                        <td>Tgl :</td>
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
