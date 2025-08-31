<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-4">
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        @if ($sal->outlet?->outlet_image)
            <div class="w-full flex items-center justify-center"><img
                    src="{{ asset('storage/images/' . $sal->outlet->outlet_image) }}" class="w-28"
                    onerror="this.src='{{ asset('img/default_img.jpg') }}'"></div>
        @endif
        <div class="text-center mb-6 border-b pb-4">
            {{-- <h1 class="text-2xl font-bold text-gray-800">STRUK</h1> --}}
            {{-- <hr class="border-gray-400"> --}}
            <table class="w-full mt-2">
                <tr>
                    <td colspan="2">
                        <h6 class="text-center text-lg font-bold">
                            {{ $sal->outlet->name }}
                        </h6>
                    </td>
                </tr>
                <tr class="text-sm text-gray-500">
                    <td style="width: 90%; text-align: center">
                        <p style="text-align: center; margin-bottom: 0px; font-size: 16px">
                            {{ $sal->outlet->address }}
                        </p>
                        <p style="padding: 0; text-align: center; margin-bottom: 0px; font-size: 16px">
                            {{ $sal->outlet->phone }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="mb-4">
            <table class="w-full text-sm ">
                <tbody>
                    <tr>
                        <th class="text-left pr-4">Customer</th>
                        <td class="text-left capitalize">{{ $sal->customer?->name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Kasir</th>
                        <td class="text-left capitalize">{{ $sal->user?->users_name }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4 w-1/4">Order ID</th>
                        <td class="text-left">{{ $sal->sale_code }}</td>
                    </tr>
                    <tr>
                        <th class="text-left pr-4">Time</th>
                        <td class="text-left">{{ \carbon\Carbon::parse($sal->sale_date)->format('d M Y H:i') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Items</h2>
            <table class="w-full mt-2 border-collapse border-t">
                <thead>
                    <tr class="border-b">
                        <th class="text-center text-sm font-medium text-gray-600 py-1">Qty</th>
                        <th class="text-center text-sm font-medium text-gray-600 py-1">Harga</th>
                        <th class="text-center text-sm font-medium text-gray-600 py-1">Disc</th>
                        <th class="text-end text-sm font-medium text-gray-600 py-1">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sal->salesDetails as $item)
                        <tr>
                            <td class="text-sm py-1" colspan="4">
                                <h5 class="text-sm  text-gray-700">
                                    {{ $item->product->name }}
                                </h5>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="text-center text-sm py-1">{{ $item->qty }}</td>
                            <td class="text-center text-sm py-1">
                                @if ($item->price == $item->final_price)
                                    Rp {{ number_format($item->price) }}
                                @else
                                    Rp {{ number_format($item->final_price) }}
                                @endif
                            </td>
                            <td class="text-center text-sm py-1">
                                @if ($item->discount > 0)
                                    <span>{{ $item->discount }}%</span>
                                @endif
                            </td>
                            <td class="text-right text-sm py-1">
                                Rp {{ number_format($item->subtotal) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="mt-4 w-full">
                <tbody>
                    <tr>
                        <th class="text-left text-sm font-medium py-1" style="width: 30%">Total</th>
                        <td class="text-right text-sm py-1" style="width: 70%">
                            Rp {{ number_format($sal->nominal_amount) }}
                        </td>
                    </tr>

                    @if ($sal->transaction_fees > 0)
                        <tr>
                            <th class="text-left text-sm font-medium py-1" style="width: 30%">
                                ADM ({{ $sal->payment_method->name }})
                            </th>
                            <td class="text-right text-sm py-1" style="width: 70%">
                                Rp {{ number_format($sal->transaction_fees) }}
                            </td>
                        </tr>
                    @endif

                    @if ($sal->discount_amount > 0)
                        @if ($sal->discount_type == 'nominal')
                            <tr>
                                <th class="text-left text-sm font-medium py-1" style="width: 30%">Diskon</th>
                                <td class="text-right text-sm py-1" style="width: 70%">
                                    Rp {{ number_format($sal->discount_amount) }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th class="text-left text-sm font-medium py-1" style="width: 30%">
                                    Diskon ({{ $sal->discount_amount }}%)
                                </th>
                                <td class="text-right text-sm py-1" style="width: 70%">
                                    Rp {{ number_format($sal->nominal_amount * ($sal->discount_amount / 100)) }}
                                </td>
                            </tr>
                        @endif
                    @endif

                    <tr>
                        <th class="text-left text-sm font-medium py-1" style="width: 30%">Bayar</th>
                        <td class="text-right text-sm py-1" style="width: 70%">
                            Rp {{ number_format($sal->nominal_pay) }}
                        </td>
                    </tr>

                    <tr>
                        <th class="text-left text-sm font-medium py-1" style="width: 30%">Kembali</th>
                        <td class="text-right text-sm py-1" style="width: 70%">
                            Rp {{ number_format($sal->nominal_pay - $sal->final_amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <div class="mt-4 border-t pt-4">
            <div class="flex justify-between text-lg font-semibold">
                <span>Total:</span>
                <span>Rp {{ number_format($sal->final_amount) }}</span>
            </div>
        </div>

        <div class="text-center mt-6 text-sm text-gray-500">
           {!! $sal->custom_footer !!}
        </div>
        <div class="text-center mt-3 text-sm text-gray-500">
          Powered by {{ $sal->powered_by }}
        </div>
    </div>
</body>

</html>
