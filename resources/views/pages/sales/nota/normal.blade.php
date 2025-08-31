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
                            <!-- header -->
                            <h2 class="center">INVOICE</h2>
                            <br>
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
                                    <td style="width: 45%; border-bottom: 1px solid #000; padding: 5px;">
                                        <h3 style="text-align: left;">
                                            {{ $company->name }}
                                        </h3>
                                        <p style="padding: 0;text-align: left;">
                                            {{ $company->address }}
                                        </p>

                                        {{--                                        <p style="padding: 0;text-align: left;"> --}}
                                        {{--                                            {{$company->about}} --}}
                                        {{--                                        </p> --}}

                                    </td>
                                    <td
                                        style="width: 45%; border-bottom: 1px solid #000; padding: 5px;text-align: left;">
                                        <table style="width: 100%; border: 0;">
                                            <tr>
                                                <td style="width: 20%;text-align: left;">Nomor</td>
                                                <td style="width: 5%;text-align: left;">:</td>
                                                <td style="width: 75%;text-align: left;">
                                                    {{ $transaction->sale_code }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal</td>
                                                <td>:</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($transaction->sale_date)->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pembayaran</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $transaction->paymentMethod != null ? $transaction->paymentMethod->name : '-' }}
                                                </td>
                                            </tr>

                                            @if ($transaction->paymentMethod?->name == 'TEMPO')
                                                <tr>
                                                    <td>Jatuh Tempo</td>
                                                    <td>:</td>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($transaction->due_date)->translatedFormat('d M Y') }}
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td>OUTLET</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $transaction->outlet->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Customer</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $transaction->customer->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>HP</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $transaction->customer->phone }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $transaction->customer->address }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- header -->
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <!-- body -->
                            <table class="border-cust">

                                <tr>
                                    <th class="center border-btm border-tp">Nama Item</th>
                                    <th class="center border-btm border-tp">Qty</th>
                                    <th class="center border-btm border-tp">Harga</th>
                                    <th class="center border-btm border-tp">Diskon (%)</th>
                                    <th class="center border-btm border-tp">Subtotal</th>
                                </tr>

                                <tbody>
                                    @foreach ($transaction->salesDetails as $row)
                                        <tr>
                                            <td>
                                                @if ($row->is_item_bundle)
                                                    <span style="padding: 10px; 0 10px 0">-</span>
                                                @endif
                                                {{ $row->product_name }}
                                            </td>
                                            <td class="center">
                                                {{ $row->qty }}
                                            </td>
                                            @if (!$row->is_item_bundle)
                                                <td class="right">
                                                    {{ rupiah($row->final_price) }}
                                                </td>
                                                <td class="right">
                                                    {{ $row->discount }}
                                                </td>
                                                <td class="right">
                                                    {{ rupiah($row->qty * $row->final_price) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <table>
                                <tbody>
                                    {{-- <tr>
                                        <th class="right" style="width: 80%;">Total</th>
                                        <th class="right">
                                            {{ rupiah($transaction->final_amount) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="right">DPP</th>
                                        <th class="right">
                                            {{ rupiah($transaction->dpp) }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="right">PPN 11%

                                        </th>
                                        <th class="right">
                                            {{ rupiah($transaction->ppn) }}

                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="right">PPH
                                            {{ $transaction->customer->npwp != null ? '0.5%' : '1%' }}

                                        </th>
                                        <th class="right">
                                            {{ rupiah($transaction->pph) }}

                                        </th>
                                    </tr> --}}
                                    <tr>
                                        <th class="right">Total Penjualan</th>
                                        <th class="right">
                                            {{ rupiah($transaction->final_amount) }}

                                        </th>
                                    </tr>
                                    <tr>
                                        <td class="right" colspan="2">Terbilang :
                                            {{ terbilang($transaction->final_amount) }} Rupiah
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- body -->
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
                                            Diterima Oleh
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
                                        </td>
                                        <td class="center border-cust">
                                            Pengirim
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
                                        </td>
                                        <td class="center border-cust">
                                            Hormat Kami
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
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
</body>

</html>
