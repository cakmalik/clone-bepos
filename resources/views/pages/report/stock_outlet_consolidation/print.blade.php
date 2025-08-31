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
                                    <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
                                        <h1 style="text-align: center;">
                                            {{ $company->name }}
                                        </h1>
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->address }}
                                        </p>
                                    
                                        <p style="padding: 0;text-align: center;">
                                            {{ $company->about }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </th>


                </tr>
            </thead>
            <tbody class="report-content">
                <tr>
                    <td class="report-content-cell">
                        <div class="main">
                            <h3 class="report-title">{{ $title }}</h3>
                            <br>
                            <table>
                                <tr class="border-cust">
                                    <th class="center">KODE PRODUK</th>
                                    <th class="center">NAMA PRODUK</th>
                                    <?php $total_column = 2; ?>
                                    @foreach($selected_inventory as $value)
                                        <?php ++$total_column ?>
                                        <th class="center">{{ $value->name }}</th>
                                    @endforeach
                                    <th>Total</th>
                                </tr>
                                
                                @forelse($products as $value)
                                <tr class="border-lr">
                                    <td>{{ $value['product_code'] }}</td> 
                                    <td>{{ $value['product_name'] }}</td>
                                    <?php $total = 0; ?>
                                    @foreach($selected_inventory as $inven)
                                        @if(array_key_exists('qty_'.$inven->id, $value))
                                        <td class="center">{{ numberGroupComma($value['qty_'.$inven->id]) }}</td>
                                        <?php $total += $value['qty_'.$inven->id] ?>
                                        @else 
                                        <td class="center">0</td>
                                        @endif
                                    @endforeach
                                    <td>{{ numberGroupComma($total) }}</td>
                                </tr>
                                @empty
                                    <tr class="border-cust">
                                        <td class="center" colspan="{{ ++$total_column }}">Tidak Ada Data Ditemukan</td>
                                    </tr>
                                @endforelse
                                <tr class="border-lr border-cust"></tr>
                            </table>
                            <br>
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