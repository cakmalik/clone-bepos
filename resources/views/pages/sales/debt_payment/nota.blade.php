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
                            <h2 class="center">TAGIHAN TEMPO</h2>
                            <br>
                            <table class="handover" style="width: 100%; margin-bottom: 5px; border: 0;">
                                <tr>
                                    <!-- Logo Perusahaan -->
                                    <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
                                        @if ($company->image)
                                            <img src="{{ asset('storage/images/' . $company->image) }}" alt="" width="70" height="70">
                                        @else
                                            <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70" height="70">
                                        @endif
                                    </td>
                            
                                    <!-- Nama & Alamat Perusahaan -->
                                    <td style="width: 45%; border-bottom: 1px solid #000; padding: 5px;">
                                        <h3 style="margin: 0; text-align: left;">{{ $company->name }}</h3>
                                        <p style="margin: 0; text-align: left;">{{ $company->address }}</p>
                                    </td>
                            
                                    <!-- Info Transaksi -->
                                    <td style="width: 45%; border-bottom: 1px solid #000; padding: 5px; text-align: left;">
                                        <table style="width: 100%; border: 0;">
                                            <tr>
                                                <td style="width: 25%;">ID Transaksi</td>
                                                <td style="width: 5%;">:</td>
                                                <td style="width: 70%;">{{ $sales->sale_code }}</td>
                                            </tr>
                                            <tr>
                                                <td>Kasir</td>
                                                <td>:</td>
                                                <td>{{ $sales->user->users_name }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pelanggan</td>
                                                <td>:</td>
                                                <td>{{ $sales->customer->name }}</td>
                                            </tr>
                                            <tr>
                                                <td>TGL Transaksi</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($sales->created_at)->format('d/m/Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td>TGL Tempo</td>
                                                <td>:</td>
                                                <td>{{ \Carbon\Carbon::parse($sales->due_date)->format('d/m/Y') }}</td>
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
                                    <th class="center border-btm border-tp">No</th>
                                    <th class="center border-btm border-tp" width="10%">Tanggal</th>
                                    <th class="center border-btm border-tp" width="15%">User Input</th>
                                    <th class="center border-btm border-tp" width="20%">Pembayaran</th>
                                    <th class="center border-btm border-tp">Keterangan</th>
                                    <th class="center border-btm border-tp">Nominal</th>
                                </tr>

                                <tbody>
                                    @forelse ($salesPayment as $payment)
                                        <tr class="border-lr">
                                            <td class="center">{{ $loop->iteration }}</td>
                                            <td class="center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</td>
                                            <td class="center">{{ $payment->user->users_name }}</td>
                                            <td class="center">{{ $payment->paymentMethod->name }}</td>
                                            <td class="left">{{ $payment->description }}</td>
                                            <td class="right">Rp {{ number_format($payment->nominal_payment, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="center">Tidak ada data ditemukan</td>
                                        </tr>
                                    @endforelse
                                    <tr class="border-cust border-lr">
                                        <td colspan="5" class="right"><b>Total Pembayaran </b></td>
                                        <td colspan="1" class="right"><b>{{ rupiah($totalPayment) }}</b></td>
                                    </tr>
                                    <tr class="border-cust border-lr">
                                        <td colspan="5" class="right"><b>Sisa Piutang </b></td>
                                        <td colspan="1" class="right"><b>{{ rupiah($sales->final_amount - $totalPayment) }}</b></td>
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
                                            ({{ auth()->user()->users_name }})
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
