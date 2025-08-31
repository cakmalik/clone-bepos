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
                            <h2 class="center">Permintaan Pembelian</h2>
                            <br>
                            <table class="handover" style="width: 100%; margin-bottom: 5px; border: 0;">
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
                                    </td>
                                    <td
                                        style="width: 45%; border-bottom: 1px solid #000; padding: 5px;text-align: left;">
                                        <table style="width: 100%; border: 0;">
                                            <tr>
                                                <td style="width: 25%;text-align: left;">Nomor
                                                    PR</td>
                                                <td style="width: 5%;text-align: left;">:</td>
                                                <td style="width: 75%;text-align: left;">
                                                    {{ $purchase->code }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Tanggal</td>
                                                <td>:</td>
                                                <td>
                                                    {{ dateWithTime($purchase->purchase_date) }}
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
                                    <th class="center border-btm border-tp">Subtotal</th>
                                </tr>

                                <tbody>
                                    @foreach ($purchase->purchaseDetailsNota as $pd)
                                        <tr class="border-lr">
                                            <td class="center">{{ $pd->product_name }}</td>
                                            <td class="center">{{ $pd->qty }}</td>
                                            <td class="center">{{ rupiah($pd->price) }}</td>
                                            <td class="right">{{ rupiah($pd->subtotal) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="border-cust border-lr">
                                        <td class="right" style="width: 80%;" colspan="3"><b>Total</b></td>
                                        <td class="right">
                                            <b>
                                                {{ rupiah($sum) }}
                                            </b>
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
                                        <th class="right" colspan="2" style="width: 50%;">
                                            &nbsp;
                                        </th>
                                        <td class="center border-cust">
                                            Menyetujui
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            (.................................)
                                        </td>
                                        <td class="center border-cust">
                                            Dibuat Oleh
                                            <br>
                                            <br>
                                            <br>
                                            <br>
                                            ({{ $purchase->user?->users_name }})
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
