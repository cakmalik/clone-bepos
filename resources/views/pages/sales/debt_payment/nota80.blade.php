<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Struk Tagihan Tempo</title>
    <style>
        * {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
        }

        body {
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 2px 0;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }

            body {
                margin: 0 auto;
            }
        }
    </style>
</head>

<body style="padding-top: 60px; padding-bottom: 60px;" class="bold uppercase">

    <div class="center" style="margin-bottom: 8px;">
        <h3 style="margin: 0;">{{ $company->name }}</h3>
        <small>{{ $company->address }}</small>
        <div class="divider"></div>
        <h4 style="margin: 6px 0 0;">TAGIHAN TEMPO <br> {{ $sales->sale_code }}</h4>
    </div>

    <div style="margin-bottom: 8px;">
        <table>
            <tr>
                <td>Pelanggan</td>
                <td style="width: 10px;">:</td>
                <td>{{ $sales->customer->name }}</td>
            </tr>
            <tr>
                <td>TGL Transaksi</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($sales->created_at)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>TGL Tempo</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($sales->due_date)->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div style="margin-bottom: 8px;">
        <table>
            <tr>
                <th class="left">#</th>
                <th class="right">Nominal</th>
            </tr>
            @foreach ($salesPayment as $payment)
            <tr>
                <td>{{ $loop->iteration }}.</td>
                <td class="right">Rp {{ number_format($payment->nominal_payment, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <div class="divider"></div>

    <div style="margin-bottom: 8px;">
        <table>
            <tr>
                <td class="left">Total Bayar</td>
                <td class="right">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="left">Hutang</td>
                <td class="right">Rp {{ number_format($sales->final_amount - $totalPayment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="left">Total Tagihan</td>
                <td class="right">Rp {{ number_format($sales->final_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="center" style="margin-top: 10px;">
        <strong>Terima Kasih</strong><br>
        <small>Powered by Kashir.ID</small>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>
