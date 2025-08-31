<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    {{--    @include('layouts.nota.style-receipt') --}}
    <style>

    </style>

</head>

<body>
    <div class="print">

        <!-- header -->

        <table style="margin-bottom: 5px; border: 0;">
            <tr style="padding: 0;">
                <td colspan="2">
                    <h2 class="center">INVOICE</h2>
                </td>
            </tr>
            <tr style="padding: 0;">
                <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                    @if ($company->image)
                        <img src="{{ asset('storage/images/' . $company->image) }}" alt="" width="70"
                            height="70">
                    @else
                        <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70" height="70">
                    @endif
                </td>
                <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
                    <h3 style="text-align: left;"> {{ $company->name }}</h3>
                    <p style="padding: 0;text-align: left"> {{ $company->address }}</p>
                    {{--                    <p style="padding: --}}
                    {{--                        0;text-align: left;"> {{$company->about}}</p> --}}
                </td>
            </tr>
        </table>

        <table style="border: 0;">
            <tr>
                <td class="left" style="width: 20%;text-align: left;">Nomor</td>
                <td class="left" style="width: 5%;text-align: left;">:</td>
                <td class="left" style="width: 75%;text-align: left;"> {{ $transaction->sale_code }} @if ($transaction->status == 'VOID')
                        <span class="text-red">(VOID)</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="left">Tanggal</td>
                <td class="left">:</td>
                <td class="left">{{ dateWithTime($transaction->sale_date) }}</td>
            </tr>
            <tr>
                <td class="left">Pembayaran</td>
                <td class="left">:</td>
                <td class="left">
                    {{ $transaction->paymentMethod != null ? $transaction->paymentMethod->name : '-' }}
                </td>
            </tr>
            <tr>
                <td class="left">Outlet</td>
                <td class="left">:</td>
                <td class="left">{{ $transaction->outlet->name }}</td>
            </tr>
            <tr>
                <td class="left">Customer</td>
                <td class="left">:</td>
                <td class="left">{{ $transaction->customer->name }}</td>
            </tr>
            <tr>
                <td class="left">HP</td>
                <td class="left">:</td>
                <td class="left">{{ $transaction->customer->phone }}</td>
            </tr>
            <tr>
                <td class="left">Alamat</td>
                <td class="left">:</td>
                <td class="left">{{ $transaction->customer->address }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th class="center border-btm border-tp">Qty</th>
                <th class="center border-btm border-tp">Harga</th>
                <th class="center border-btm border-tp">Diskon (%)</th>
                <th class="center border-btm border-tp">Subtotal</th>
            </tr>

            <tbody>
                @foreach ($transaction->salesDetails as $row)
                    <tr>
                        <th class="left" colspan="4">
                            {{ $row->product_name }}
                        </th>
                    </tr>
                    <tr>
                        <td class="center" id="qty">{{ $row->qty }}</td>
                        <td class="right">{{ rupiah($row->final_price) }}</td>
                        <td class="right"> {{ $row->discount }}</td>
                        <td class="right"> {{ rupiah($row->subtotal) }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th class="right">Total</th>
                    <th class="right">{{ rupiah($transaction->final_amount) }}</th>
                </tr>
                <tr>
                    <th class="right">DPP</th>
                    <th class="right">{{ rupiah($transaction->dpp) }}</th>
                </tr>
                <tr>
                    <th class="right">PPN</th>
                    <th class="right">{{ rupiah($transaction->ppn) }}</th>
                </tr>
                <tr>
                    <th class="right">PPH</th>
                    <th class="right"> {{ rupiah($transaction->pph) }}</th>
                </tr>
                <tr>
                    <th class="right">Total Penjualan</th>
                    <th class="right">{{ rupiah($transaction->final_amount) }}</th>
                </tr>
                <tr>
                    <td class="right" colspan="2">Terbilang : {{ terbilang($transaction->final_amount) }}</td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <td class="center">
                        Diterima Oleh
                        <br>
                        <br>
                        <br>
                        <br>
                        (.................................)
                    </td>
                    <td class="center">
                        Pengirim
                        <br>
                        <br>
                        <br>
                        <br>
                        (.................................)
                    </td>
                </tr>
                <tr>
                    <td class="center" colspan="2">
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
    </div>
</body>

</html>
