<html>
<body>
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
                    <td>{{ $value->nominal }}</td>
                    <td>{{ $value->nominal_returned }}</td>
                    <td>{{ $value->nominal_paid }}</td>
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
                    <td >{{ $totalInvoice }}</td>
                    <td>{{ $totalReturned }}</td>
                    <td>{{ $totalPaid }}</td>
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