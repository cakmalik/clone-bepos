<table>
    <tr>
        <td>Periode</td>
        <td style="text-align: right; padding-right: 10px;">:</td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td>Penjualan</td>
        <td style="text-align: right; padding-right: 10px;">:</td>
        <td>Rp{{ $summary }}</td>
    </tr>
    <tr>
        <td>Retur</td>
        <td style="text-align: right; padding-right: 10px;">:</td>
        <td>Rp{{ $return }}</td>
    </tr>
    <tr>
        <td>Pendapatan</td>
        <td style="text-align: right; padding-right: 10px;">:</td>
        <td>Rp{{ $revenue }}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>ID TRANSAKSI</th>
            <th>Tanggal</th>
            <th>Outlet</th>
            <th>Pelanggan</th>
            <th>Status</th>
            <th>Total transaksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sales as $i)
        @php    
            if ($i->ref_code != null && $i->status == 'success'){
                $status = 'Retur';
            } else if ($i->status == 'success') {
                $status = 'Sukses';
            } else if ($i->status == 'draft') {
                $status = 'Draft';
            }
        @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $i->sale_code }}</td>
                <td>{{ $i->sale_date }}</td>
                <td>{{ $i->outlet?->name }}</td>
                <td>{{ $i->customer?->name == 'Walk-in-customer' ? '-' : $i->customer?->name }}</td>
                <td>{{ $status }}</td>
                <td>{{ $i->final_amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
