<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Invoice Report</title>
    <style>
        body {
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            /* This makes borders collapse into one another */
            width: 100%;
        }

        th,
        td {
            border: 1px solid black;
            /* This sets a solid black border around cells */
            padding: 8px;
            text-align: left;
        }
        
    </style>
</head>

<body>
    <h2>Data Purchase Invoice</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No.Tagihan</th>
                <th>Kode PO</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Nominal</th>
                <th>Retur</th>
                <th>Dibayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalInvoice = 0;
                $totalPaid = 0;
                $totalReturned = 0;
            @endphp

            @if ($purchaseInvoices->isNotEmpty())
                @foreach($purchaseInvoices as $key => $value)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $value->invoice_number }}</td>
                    <td>{{ $value->po_code }}</td>
                    <td>{{ $value->invoice_date }}</td>
                    <td>{{ optional($value->purchase->supplier)->name }}</td>
                    <td>{{ $value->nominal_rp }}</td>
                    <td>{{ $value->nominal_returned_rp }}</td>
                    <td>{{ $value->nominal_paid_rp }}</td>
                    <td>{{ $value->is_done }}</td>

                    @php
                        $totalInvoice += $value->nominal;
                        $totalReturned += $value->nominal_returned;
                        $totalPaid += $value->nominal_paid;
                    @endphp
                </tr>
                @endforeach
                <tr>
                    <td colspan="5">Total</td>
                    <td >{{ rupiah($totalInvoice) }}</td>
                    <td>{{ rupiah($totalReturned) }}</td>
                    <td>{{ rupiah($totalPaid) }}</td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan="9">Belum ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>

</html>