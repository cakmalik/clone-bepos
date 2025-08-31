<html>

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="viewport" content="width=device-width">
    @include('layouts.nota.style-normal')
</head>

<body onload="window.print()">
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
                            <h3 class="report-title">LAPORAN VOID PENJUALAN</h3>
                            <p>Mulai : {{ $start_date }}</p>
                            <p>Hingga : {{ $end_date }}</p>
                            <table>
                                <thead>
                                    <tr class="border-cust">
                                        <th>Waktu</th>
                                        <th>Outlet</th>
                                        <th>Kasir</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                        {{-- <th>ITEM</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $total_qty = 0;
                                    @endphp
                                    @forelse ($data as $i)
                                        @php
                                            $total += $i->subtotal;
                                            $total_qty += $i->qty;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($i->created_at)->translatedFormat('d M Y H:i') }}
                                            </td>
                                            <td class="text-capitalize">{{ $i->outlet?->name }}</td>
                                            <td class="text-capitalize">{{ $i->user?->users_name }}</td>
                                            <td class="text-capitalize">{{ $i->product_name }}</td>
                                            <td class="">Rp{{ number_format($i->final_price) }}</td>
                                            <td class="center">{{ number_format($i->qty) }}</td>
                                            <td class="">Rp{{ number_format($i->subtotal) }}</td>
                                            {{-- <td class="text-capitalize"> <a href="#" class="btn btn-sm btn-info" id="detail_sales"
                                                data-detail="{{ $i->id }}">
                                                <li class="fas fa-eye me-1" aria-hidden="true"></li>Detail
                                            </a></td> --}}
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-cust"
                                        style="background-color: gray; font-weight: bold; color: white">
                                        <td colspan="5" class="text-right">Total</td>
                                        <td class="center">{{ number_format($total_qty) }}</td>
                                        <td>Rp{{ number_format($total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
