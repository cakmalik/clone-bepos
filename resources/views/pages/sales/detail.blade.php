<a href="" class="btn btn-info btn-sm py-2 px-3" data-bs-toggle="modal"
    data-bs-target="#product_unit-detail{{ $i->id }}">
    <li class="fas fa-eye"></li>
</a>
<div class="modal modal-blur fade" id="product_unit-detail{{ $i->id }}" tabindex="-1" role="dialog"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead id="sales_detail">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Discount</th>
                            <th>Qty</th>
                            <th>SubTotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($i->salesDetails as $detail)
                            <tr>
                                <td>{{ $detail->product_name }}</td>
                                <td>{{ number_format($detail->final_price) }}</td>
                                <td>{{ $detail->discount }}</td>
                                <td>{{ floatval($detail->qty) }}</td>
                                <td>Rp {{ number_format($detail->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
