<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kashir</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        table {
            width: 20%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: center;
            border: 1px solid gray;
            padding: 10px;
        }
    </style>
</head>

<body>
    <table>
        @for ($i = 0; $i < $jumlah; $i++)
            @if ($i % 5 === 0)
                @if ($i > 0)
                    </tr> <!-- Tutup baris sebelumnya -->
                @endif
                <tr> <!-- Buka baris baru setiap 5 elemen -->
            @endif
            <td>
                <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG(strval($barcode), 'C128') !!}" style="max-width: 120px" />
                <br>
                <small>{{ $name }}</small>
            </td>
        @endfor
        </tr> <!-- Tutup baris terakhir -->
    </table>
</body>

</html>
