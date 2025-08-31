<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('layouts.nota.style-normal')
    <style>
        .border-cust td {
            border: 1px solid #000;
        }
    </style>
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
                                    <td style="width: 45%; border-bottom: 1px solid #000; padding: 5px;"
                                        class="text-center">
                                        <h2 class="center" style="margin-bottom: 12px;">{{ $title }}</h2>
                                        <h3 style="text-align: center;">
                                            {{ $company->name }}
                                        </h3>
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->address }}
                                        </p>
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
                                    <th class="center border-btm border-tp">Kode Produk</th>
                                    <th class="center border-btm border-tp">Nama Produk</th>
                                    <th class="center border-btm border-tp">Stok</th>
                                    <th class="center border-btm border-tp">Harga Beli</th>
                                    <th class="center border-btm border-tp">Nilai Stok</th>
                                    @foreach ($selling_prices as $selling_price)
                                        <th class="center border-btm border-tp">Harga {{ $selling_price->name }}</th>
                                        <th class="center border-btm border-tp">Nilai Stok {{ $selling_price->name }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tbody>
                                    @php
                                        $selling_prices_totals = [];
                                    @endphp
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product['code'] }}</td>
                                            <td>{{ $product['name'] }}</td>
                                            <td>{{ $product['product_stock_sum_stock_current'] }}</td>
                                            <td>{{ currency($product['capital_price']) }}</td>
                                            @php
                                                $capitalStockValue = $product['product_stock_sum_stock_current'] * $product['capital_price'];
                                                if (isset($selling_prices_totals[0])) {
                                                    $selling_prices_totals[0] += $capitalStockValue;
                                                } else {
                                                    $selling_prices_totals[0] = $capitalStockValue;
                                                }
                                            @endphp
                                            <td>{{ currency($capitalStockValue) }}</td>
                                            @foreach ($selling_prices as $selling_price)
                                                @php
                                                    $stockValue = $product['product_stock_sum_stock_current'] * ($product['pricesObject'][$selling_price->id] ?? 0);
                                                    if (isset($selling_prices_totals[$selling_price->id])) {
                                                        $selling_prices_totals[$selling_price->id] += $stockValue;
                                                    } else {
                                                        $selling_prices_totals[$selling_price->id] = $stockValue;
                                                    }
                                                @endphp
                                                <td>{{ currency($product['pricesObject'][$selling_price->id] ?? 0) }}
                                                </td>
                                                <td>{{ currency($stockValue) }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    @php
                                    @endphp
                                    <tr style="font-weight: 800;">
                                        <td colspan="3" style="text-align: right;">
                                            Total
                                        </td>
                                        <td></td>
                                        <td>{{ rupiah($selling_prices_totals[0]) }}</td>
                                        @foreach ($selling_prices as $selling_price)
                                            <td></td>
                                            <td>{{ rupiah($selling_prices_totals[$selling_price->id]) }}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
                {{--            </tbody> --}}
                {{--            <tfoot class="report-footer"> --}}
                {{--                <tr> --}}
                {{--                    <td class="report-footer-cell"> --}}
                {{--                        <div class="footer-info"> --}}
                {{--                            <!-- footer --> --}}
                {{--                            <table> --}}
                {{--                                <tbody> --}}
                {{--                                    <tr> --}}
                {{--                                        <th class="right" colspan="2" style="width: 50%;">&nbsp; --}}
                {{--                                        </th> --}}
                {{--                                        <td class="center border-cust"> --}}
                {{--                                            Diterima Oleh --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            (.................................) --}}
                {{--                                        </td> --}}
                {{--                                        <td class="center border-cust"> --}}
                {{--                                            Pengirim --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            (.................................) --}}
                {{--                                        </td> --}}
                {{--                                        <td class="center border-cust"> --}}
                {{--                                            Hormat Kami --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            <br> --}}
                {{--                                            (.................................) --}}
                {{--                                        </td> --}}
                {{--                                    </tr> --}}
                {{--                                </tbody> --}}
                {{--                            </table> --}}
                {{--                            <!-- footer --> --}}
                {{--                        </div> --}}
                {{--                    </td> --}}
                {{--                </tr> --}}
                {{--            </tfoot> --}}
        </table>
    </div>
</body>

</html>
