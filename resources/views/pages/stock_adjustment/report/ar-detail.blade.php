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
                            <!-- header -->
                            <h2 class="center">Stock Adjustment</h2>
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

                                        {{-- <p style="padding: 0;text-align: left;">
                                            <span>{{ $company->bank }}
                                                {{ $company->account_number }} a.n
                                                {{ $company->name }}</span>
                                        </p> --}}

                                    </td>
                                    <td
                                        style="width: 45%; border-bottom: 1px solid #000; padding: 5px;text-align: left;">
                                        <table style="width: 100%; border: 0;">
                                            <tr>
                                                <td style="width: 25%;text-align: left;">
                                                    Nomor Adj</td>
                                                <td style="width: 5%;text-align: left;">:</td>
                                                <td style="width: 75%;text-align: left;">
                                                    {{ $adjustment->code }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Nomor SO</td>
                                                <td>:</td>
                                                <td>
                                                    {{ $adjustment->ref_code }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>Tanggal</td>
                                                <td>:</td>
                                                <td>
                                                    {{ dateWithTime($adjustment->so_date) }}
                                                </td>
                                            </tr>
                                            @if ($adjustment->inventory_id)
                                                <tr>
                                                    <td>Gudang</td>
                                                    <td>:</td>
                                                    <td>
                                                        {{ $adjustment->inventory->name }}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td>Outlet</td>
                                                    <td>:</td>
                                                    <td>
                                                        {{ $adjustment->outlet->name }}
                                                    </td>
                                                </tr>
                                            @endif

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
                            <table>
                                <tr class="border-cust">
                                    <th class="center border-btm border-tp">Kode Produk</th>
                                    <th class="center border-btm border-tp">Nama Produk</th>
                                    <th class="center border-btm border-tp">Satuan</th>
                                    <th class="center border-btm border-tp">Kategori</th>
                                    <th class="center border-btm border-tp">Qty System</th>
                                    <th class="center border-btm border-tp">Qty SO</th>
                                    <th class="center border-btm border-tp">Qty Adjustment</th>
                                    <th class="center border-btm border-tp">Qty Akhir</th>
                                    <th class="center border-btm border-tp">Nominal Adjustment
                                    </th>
                                </tr>

                                <tbody>
                                    @foreach ($adjustment->adjustment_detail as $adj)
                                        @if ($adj->qty_adjustment != 0)
                                            <tr class="border-cust">
                                                <td class="center">{{ $adj->product->code }}
                                                </td>
                                                <td class="center">{{ $adj->product->name }}
                                                </td>
                                                <td class="center">
                                                    {{ $adj->product->productUnit->symbol }}
                                                </td>
                                                <td class="center">
                                                    {{ $adj->product->productCategory->name }}
                                                </td>
                                                <td class="center">
                                                    {{ numberGroup($adj->qty_system) }}
                                                </td>
                                                <td class="center">
                                                    {{ numberGroup($adj->qty_so) }}
                                                </td>
                                                <td class="center">
                                                    {{ numberGroup($adj->qty_adjustment) }}
                                                </td>
                                                <td class="center">
                                                    {{ numberGroup($adj->qty_after_adjustment) }}
                                                </td>
                                                <td class="right">
                                                    {{ rupiah($adj->adjustment_nominal_value) }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach

                                    <tr>
                                        <td class="right" colspan="8"><b>Total</b></td>
                                        <td class="right">
                                            <b> {{ rupiah($sum) }}</b>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <table>
                                <tbody>


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
                                            ({{ $adjustment->user->username }})
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
