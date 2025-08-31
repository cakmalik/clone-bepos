<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print A4 Page with Price Tags</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                visibility: hidden;
            }

            #printable-area {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
            }
        }

        .page {
            page-break-after: always;
            width: 210mm;
            max-height: 297mm;
            margin: 0;
            padding: 0mm;
            box-sizing: border-box;
            border: 0px solid #ccc;
            display: flex;
            flex-wrap: wrap;
            position: relative;
        }

        /* .price-tag {
            width: 50mm;
            height: 25mm;
            border: 1px solid #000;
            margin: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        } */

        .price-tag {
            width: 50mm;
            height: 25mm;
            border: 1px solid #000;
            margin: 0px;
            display: flex;
            flex-direction: column;
            /* Display content vertically */
            align-items: center;
            justify-content: center;
            font-size: 16px;
            /* position: absolute;
            top: 30px; */
        }
    </style>
</head>

<body>
    <div id="printable-area">
        <div class="page">
            @forelse ($products as $index => $i)
                @php
                    $brand = $i->brand ? $i->brand->name . ' / ' : '';
                    $productName = substr($brand . $i->name, 0, 18); // Truncate to maximum of 18 characters
                @endphp
                <div class="price-tag">
                    <span style="font-size: 20px;  font-weight: bold; ">Rp
                        {{ number_format($i->productPriceUtama?->price) }}</span>
                    <span class="product-name" style="font-size: 16px">{{ $productName }}</span>
                    <span class="product-name" style="font-size: 14px">{{ $i->code . '/' . $i->barcode }}</span>
                    @if (!empty($i->barcode))
                        <img style="max-width: 40mm; max-height: 5mm"
                            src="data:image/png;base64,{!! DNS1D::getBarcodePNG(strval($i->barcode), 'C128') !!}" class="barcode">
                    @else
                        <span style="color: red; font-size: 12px;">--</span>
                    @endif
                    <small id="tanggal">{{ Carbon\Carbon::now()->format('d/m/Y') }}</small>
                </div>
            @empty
            @endforelse

        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>
