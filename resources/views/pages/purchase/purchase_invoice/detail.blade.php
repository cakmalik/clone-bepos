<div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">No. Tagihan</h6>
                    <h3 class="font-weight-bold text-primary">
                        {{ $invoice->invoice_number }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Supplier</h6>
                    <h3 class="font-weight-bold text-dark">
                        {{ $invoice->purchase->supplier->name }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Tanggal Tagihan</h6>
                    <h3 class="font-weight-bold text-dark">
                        {{ \Carbon\Carbon::parse($invoice->invoice_date)->translatedFormat('d F Y') }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Status</h6>
                    <h3 class="badge px-3 py-2 rounded text-uppercase {{ $invoice->is_done == 1 ? 'bg-success-lt text-success' : 'bg-orange-lt text-orange' }}">
                        {{ $invoice->is_done ? 'LUNAS' : 'BELUM LUNAS' }}
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal Pembayaran</th>
                <th width="10%">Pembayaran</th>
                <th width="10%">Admin</th>
                <th>Keterangan</th>
                <th width="15%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoice->invoicePayments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $payment->paymentMethod->name }}</td>
                    <td>{{ $payment->user->users_name }}</td>
                    <td class="text-left">{{ $payment->description }}</td>
                    <td class="text-right">Rp {{ number_format($payment->nominal_payment, 0, ',', '.') }}</td>
                </tr>
                
            @empty
                <tr>
                    <td colspan="6" class="center">Tidak ada data ditemukan</td>
                </tr>
            @endforelse
            <tr class="text-right">
                <td colspan="5"><b>Total Pembayaran</b></td>
                <td><b>Rp {{ number_format($invoice->nominal_paid ?? 0, 0, ',', '.') }}</b></td>
            </tr>
            <tr class="text-right">
                <td colspan="5"><b>Kekurangan</b></td>
                <td><b>Rp {{ number_format($invoice->total_invoice - $invoice->nominal_paid ?? 0, 0, ',', '.') }}</b></td>
            </tr>
            
            <tr class="text-right">
                <td colspan="5"><b>Total Tagihan</b></td>
                <td><b>Rp {{ number_format($invoice->total_invoice, 0, ',', '.') }}</b></td>
            </tr>
        </tbody>
    </table>
</div>
