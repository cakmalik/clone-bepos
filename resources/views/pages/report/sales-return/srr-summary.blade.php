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
                            <h3 class="report-title">Laporan Retur Penjualan</h3>
                            <br>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">NO. RETUR</th>
                                    <th class="center">TANGGAL</th>
                                    <th class="center">NO. INVOICE</th>
                                    <th class="center">OUTLET</th>
                                    <th>TOTAL RETUR</th>
                                    <th>STATUS RETUR</th>
                                </tr>
                                <?php $total= 0; $retur_semua = 0; ?>
                                @forelse($retur as $value)
                                    <?php 
                                        $total += $value->final_amount;
                                        $retur_semua += $value->final_amount;
                                    ?>
                                    <tr class="border-lr">
                                        <td>{{ $value->sale_code }}</td>
                                        <td>{{ dateWithTime($value->sale_date) }}</td>
                                        <td>{{ $value->ref_code }}</td>
                                        <td>{{ $value->outlet_name }}</td>
                                        <td>{{ numberGroup($value->final_amount) }}</td>
                                        <td>{{ $value->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="center" colspan="6">Tidak ada data ditemukan</td>
                                    </tr>
                                @endforelse
                                
                                <tr class="border-cust border-lr">
                                    <td colspan="4" class="right"><b> RETUR SEMUA</b></td>
                                    <td>{{ rupiah($retur_semua) }}</td>
                                    <td></td>
                                </tr>
                                <tr class="border-cust border-lr">
                                    <td colspan="4" class="right"><b> TOTAL</b></td>
                                    <td>{{ rupiah($total) }}</td>
                                    <td></td>
                                </tr>
                              
                            </table>
                            <br>
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