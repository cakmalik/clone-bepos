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
                        <div class="header-info">
                            @include('layouts.nota.kop')
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">Laporan Penjualan Detail</h3>
                            <br><br>
                            @forelse($data as $row)
                                <table>
                                    <tr>
                                        <td style="width: 60px;"><b>No. Invoice</b></td>
                                        <td style="width: 10px;"><b>:</b></td>
                                        <td><b>{{ $row->sale_code }}</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal</b></td>
                                        <td><b>:</b></td>
                                        <td><b>{{ dateWithTime($row->sale_date) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Pembayaran</b></td>
                                        <td><b>:</b></td>
                                        <td>
                                            <b>{{ $row->paymentMethod->name }}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Customer</b></td>
                                        <td><b>:</b></td>
                                        <td><b>{{ $row->customer->code }} - {{ $row->customer->name }} <br>
                                                {{ $row->customer->address }} Telp. {{ $row->customer->phone }}</br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>NPWP</b></td>
                                        <td><b>:</b></td>
                                        <td><b>{{ $row->customer->npwp }}</b></td>
                                    </tr>

                                </table>
                                <br>

                                <table>
                                    <thead>
                                        <tr class="border-cust">
                                            <th>Nama Item</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Diskon</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($row->salesDetails as $item)
                                            <tr class="border-lr">
                                                <td class="">{{ $item->product_name }}</td>
                                                <td class="center">{{ formatDecimal($item->qty) }}</td>
                                                <td class="right">{{ rupiah($item->final_price) }}</td>
                                                <td class="center">{{ $item->discount }}%</td>
                                                <td class="right">{{ rupiah($item->subtotal) }} @if($item->is_retur) (RETUR) @endif</td>
                                            </tr>
                                        @endforeach


                                        <tr>
                                            <th class="right border-tp" colspan="4">Total</th>
                                            <th class="right border-tp border-rl"
                                                style="border-bottom: 1px solid #000000">
                                                {{ rupiah($row->final_amount) }}</th>
                                        </tr>
                                        {{-- <tr>
                                            <th class="right" colspan="4">PPH</th>
                                            <th class="right border-tp border-rl">{{ rupiah($row->final_amount) }}</th>
                                        </tr>
                                        <tr>
                                            <th class="right" colspan="4">Grand Total</th>
                                            <th class="right border-tp border-btm border-rl">
                                                {{ rupiah($row->grand_total) }}</th>
                                        </tr> --}}
                                    </tbody>
                                </table>
                                <br><br>
                            @empty
                                <h4 class="center">-- Tidak ada data ditemukan -- </h4>
                            @endforelse
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
