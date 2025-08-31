<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>LAPORAN PENJUALAN</title>
</head>

<body>
    @if ($reportType === 'DETAIL')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];

            $columnWidths = [
                'No.' => 50,
                'No. Invoice' => 150,
                'Tanggal' => 150,
                'CUSTOMER' => 210,
                'NAMA ITEM' => 170,
                'QTY' => 50,
                'HARGA' => 80,
                'DISKON' => 70,
                'SUBTOTAL' => 150,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php $counter = 1; @endphp
                @foreach ($data as $item)
                    @foreach ($item['sales_details'] as $salesDetail)
                        <tr>
                            <td>{{ $counter++ }}</td>
                            <td>{{ $item['sale_code'] }}</td>
                            <td>{{ $item['sale_date'] }}</td>
                            <td>{{ $item['customer']['code'] }} - {{ $item['customer']['name'] }}</td>
                            <td>{{ $salesDetail['product_name'] }}</td>
                            <td>{{ $salesDetail['qty'] }}</td>
                            <td>{{ $salesDetail['final_price'] }}</td>
                            <td>{{ $salesDetail['discount'] }}</td>
                            <td>
                                @if ($salesDetail['is_retur'])
                                    Telah DiRetur -
                                @endif
                                {{ $salesDetail['subtotal'] }}

                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'SIMPLE')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];
            $columnWidths = [
                'No.' => 50,
                'NO INVOICE' => 180,
                'TANGGAL' => 150,
                'CUSTOMER' => 150,
                'SALES' => 150,
                'PEMBAYARAN' => 150,
                'SUB TOTAL' => 150,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->sale_code }}</td>
                        <td>{{ $item->sale_date }}</td>
                        <td>{{ $item->customer_code . ' - ' . $item->customer }}</td>
                        <td>{{ $item->user }}</td>
                        <td>{{ $item->payment_method }}</td>
                        <td>{{ $item->final_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'CATEGORY')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];
            $columnWidths = [
                'No.' => 50,
                'KATEGORI' => 80,
                'QTY' => 50,
                'SUB HARGA BELI' => 130,
                'OMSET' => 90,
                'LABA' => 90,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product_category }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->subtotal_hpp }}</td>
                        <td>{{ $item->subtotal }}</td>
                        <td>{{ $item->final_profit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'CUSTOMER')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];
            $columnWidths = [
                'No.' => 50,
                'NO INVOICE' => 130,
                'TANGGAL' => 150,
                'CUSTOMER' => 130,
                'PRODUK' => 150,
                'QTY' => 40,
                'HARGA JUAL' => 90,
                'DISKON' => 90,
                'HARGA FINAL' => 90,
                'SUB TOTAL	' => 90,
                'PEMBAYARAN' => 130,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['sale_code'] }}</td>
                        <td>{{ $item['sale_date'] }}</td>
                        <td> {{ $item['customer'] }}</td>
                        <td>{{ $item['product_name'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['discount'] }}</td>
                        <td>{{ $item['final_price'] }}</td>
                        <td>{{ $item['subtotal'] }}</td>
                        <td>{{ $item['payment_method'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'SUMMARY')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];
            $columnWidths = [
                'No.' => 50,
                'NO INVOICE' => 130,
                'TANGGAL' => 150,
                'CUSTOMER' => 230,
                'PRODUK' => 150,
                'QTY' => 40,
                'HARGA JUAL	' => 90,
                'DISKON' => 90,
                'HARGA FINAL' => 90,
                'SUB TOTAL' => 90,
                'PEMBAYARAN' => 130,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->sale_code }}</td>
                        <td>{{ $item->sale_date }}</td>
                        <td>{{ $item->customer_code . ' - ' . $item->customer }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->discount }}</td>
                        <td>{{ $item->final_price }}</td>
                        <td>{{ $item->subtotal }}</td>
                        <td>{{ $item->payment_method }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @elseif($reportType === 'USER')
        @php
            $cellStyles = [
                'font' => ['bold' => true],
            ];
            $columnWidths = [
                'No.' => 50,
                'NO INVOICE' => 130,
                'TANGGAL' => 150,
                'NAMA KASIR' => 200,
                'PRODUK' => 150,
                'QTY' => 40,
                'HARGA BELI' => 90,
                'HARGA JUAL' => 90,
                'DISKON' => 90,
                'SUBTOTAL' => 90,
                'PROFIT	' => 90,
                'GRAND TOTAL' => 130,
            ];
        @endphp
        <h3>Report Penjualan Menurut by {{ $reportType }}</h3>
        <p>Downloaded at: {{ now() }}</p>
        <p></p>
        <table>
            <thead>
                <tr>
                    @foreach ($columnWidths as $title => $width)
                        <th style="width: {{ $width }}px; padding: 5px;">{{ $title }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->transaction_date }}</td>
                        <td>{{ $item->user_name }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->buy_price }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->discount }}</td>
                        <td>{{ $item->subtotal }}</td>
                        <td>{{ $item->profit }}</td>
                        <td>{{ $item->final_subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
    @endif

</body>

</html>
