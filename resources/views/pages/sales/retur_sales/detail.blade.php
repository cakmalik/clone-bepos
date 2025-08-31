<table class="table">
    <thead>
        <tr>
            <th>Produk</th>
            {{-- <th>Unit</th> --}}
            <th class="text-center">QTY Retur</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($retur->salesDetails as $detail)
            @if ($detail->is_item_bundle)
                <tr>
                    <td colspan="4" style="padding-left: 50px;">{{ $detail->product_name }} ({{ $detail->qty }}
                        {{ $detail->unit_symbol }})</td>
                </tr>
            @else
                <tr>
                    <td><strong>{{ $detail->product_name }}</strong></td>
                    <td class="text-center">{{ $detail->qty }} {{ $detail->unit_symbol }}</td>
                    <td>Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td>Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endif
        @endforeach

        <tr class="fw-bold fs-3">
            <td colspan="3">TOTAL RETUR</td>
            <td>Rp. {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="card mt-3">
    <div class="card-header">
        <strong>Alasan Retur</strong>
    </div>
    <div class="card-body">
        <p>{{ $refund_reason ?? '-' }}</p>
    </div>
</div>
