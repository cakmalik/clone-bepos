@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Penerimaan Pembelian
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                @if ($purchase->is_invoiced == 1)
                    <div class="card">
                        <form action="{{ route('purchaseReception.payment', $purchase->id) }}" method="POST" class="form-detect-unsaved">
                            @csrf
                            <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

                            <div class="pt-3 px-3">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <h4 class="mb-0">Pembayaran Faktur</h4>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('purchaseInvoice.show', $purchase->purchaseInvoice->id) }}" class="btn btn-outline-primary">
                                            <i class="fa fa-print me-2"></i> Cetak
                                        </a>
                                        @if ($purchase->purchaseInvoice->is_done == 0)
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-dollar me-2"></i> Bayar Sekarang
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body pb-0">
                                <div class="row pt-2">
                                    {{-- Card: No. Tagihan --}}
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-1">No. Tagihan</h6>
                                                <h3 class="font-weight-bold text-primary">
                                                    {{ $purchase->purchaseInvoice->invoice_number }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Card: Jumlah Tagihan --}}
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-1">Total Tagihan</h6>
                                                <h3 class="font-weight-bold text-dark">
                                                    Rp {{ number_format($purchase->purchaseInvoice->total_invoice ?? 0, 0, ',', '.') }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                            
                                    {{-- Card: Tanggal Penerimaan --}}
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-1">Tanggal Penerimaan</h6>
                                                <h3 class="font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::parse($purchase->purchaseInvoice->invoice_date)->translatedFormat('d F Y H:i') }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                            
                                    {{-- Card: Status --}}
                                    <div class="col-md-3 mb-3">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-1">Status</h6>
                                                <span class="badge px-3 py-2 rounded text-uppercase 
                                                    {{ $purchase->purchaseInvoice->is_done == 1 ? 'bg-success-lt text-success' : 'bg-orange-lt text-orange' }}">
                                                    {{ $purchase->purchaseInvoice->is_done == 1 ? 'Lunas' : 'Belum Lunas' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row pt-2">
                                    {{-- Tabel Pembayaran --}}
                                    <div class="col-md-12">
                                        <table class="table no-datatable mb-4">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="15%">Tanggal Pembayaran</th>
                                                    <th width="15%">Pembayaran</th>
                                                    <th width="10%">Admin</th>
                                                    <th>Keterangan</th>
                                                    <th width="15%">Nominal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Baris histori pembayaran --}}
                                                @foreach ($invoicePayment as $payment)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $payment->paymentMethod->name }}</td>
                                                        <td>{{ $payment->user->users_name }}</td>
                                                        <td>{{ $payment->description }}</td>
                                                        <td class="text-right">Rp {{ number_format($payment->nominal_payment, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            
                                                {{-- Baris input pembayaran baru --}}
                                                @if ($purchase->purchaseInvoice->is_done == 0)
                                                <tr class="align-middle">
                                                    <td>#</td>

                                                    {{-- Tanggal Pembayaran --}}
                                                    <td>
                                                        <input type="date"
                                                            name="payment_date"
                                                            class="form-control @error('payment_date') is-invalid @enderror"
                                                            value="{{ old('payment_date', date('Y-m-d')) }}">
                                                        @error('payment_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Metode Pembayaran --}}
                                                    <td>
                                                        <select name="payment_method_id"
                                                                class="form-control @error('payment_method_id') is-invalid @enderror">
                                                            <option value="">-- Pilih Metode Pembayaran --</option>
                                                            @foreach($paymentMethods as $value)
                                                                <option value="{{ $value->id }}" {{ old('payment_method_id') == $value->id ? 'selected' : '' }}>
                                                                    {{ $value->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('payment_method_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Nama User --}}
                                                    <td>
                                                        {{ auth()->user()->users_name }}
                                                    </td>

                                                    {{-- Keterangan --}}
                                                    <td>
                                                        <input type="text"
                                                            name="description"
                                                            class="form-control @error('description') is-invalid @enderror"
                                                            placeholder="Keterangan..."
                                                            value="{{ old('description') }}">
                                                        @error('description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>

                                                    {{-- Nominal --}}
                                                    <td>
                                                        <div class="input-group">
                                                            <button type="button" class="btn text-primary" id="btnNominalPas">
                                                                <small>PAS</small>
                                                            </button>
                                                            <input type="text"
                                                                id="nominal_payment"
                                                                name="nominal_payment"
                                                                class="form-control text-end @error('nominal_payment') is-invalid @enderror"
                                                                placeholder="0"
                                                                value="{{ old('nominal_payment') }}">
                                                        </div>
                                                        @error('nominal_payment')
                                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                </tr>
                                                @endif
                                            
                                                {{-- Ringkasan total --}}
                                                <tr class="text-right">
                                                    <td colspan="5"><b>Total Pembayaran</b></td>
                                                    <td><b>Rp {{ number_format($totalInvoice ?? 0, 0, ',', '.') }}</b></td>
                                                </tr>
                                                @if ($purchase->purchaseInvoice->is_done == 0)
                                                <tr class="text-right">
                                                    <td colspan="5"><b>Kekurangan</b></td>
                                                    <td class="text-warning"><b>- Rp {{ number_format($underPayment ?? 0, 0, ',', '.') }}</b></td>
                                                </tr>
                                                @endif
                                            </tbody>
                                            
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif


                <div class="card pt-3 pb-4">
                    <div class="pt-3 px-3">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="mb-0">Penerimaan Barang</h4>
                            <div class="d-flex gap-2">
                                {{-- <a href="#" class="btn btn-outline-primary">
                                    <i class="fa fa-print me-2"></i> Cetak
                                </a>
                                @if ($purchase->purchaseInvoice->is_done == 0)
                                <a href="#" class="btn btn-primary">
                                    <i class="fa fa-dollar me-2"></i> Bayar Sekarang
                                </a>
                                @endif --}}
                            </div>
                        </div>
                    </div>

                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Kode PN</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            {{ $purchase->code }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Penerima</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            {{ $purchase->inventory->name ?? $purchase->outlet->name }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            {{-- supplier --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Supplier</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            {{ $purchase->supplier->name }}
                                        </h3>
                                    </div>
                                </div>
                            </div>

                            {{-- status penerimaan --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Status Penerimaan</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            @if ($purchase->purchase_status == 'Draft')
                                                <span class="badge badge-sm bg-warning-lt">DRAFT</span>
                                            @elseif($purchase->purchase_status == 'Open')
                                                <span class="badge badge-sm bg-orange-lt">BELUM DITERIMA</span>
                                            @elseif($purchase->purchase_status == 'Finish')
                                                <span class="badge badge-sm bg-success-lt">SUDAH DITERIMA</span>
                                            @endif
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>


                        @if ($purchase->purchase_status == 'Open')
                        <form action="/reception" method="post" class="form-detect-unsaved">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">No.Referensi</label>
                                    <input type="text"
                                        class="form-control @error('shipment_ref_code') is-invalid @enderror"
                                        name="shipment_ref_code" autocomplete="off">
                                    @error('shipment_ref_code')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary " style="margin-top: 28px;">
                                        <i class="fa fa-check"></i>&nbsp; Terima</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3  mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Qty Sudah diterima</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Qty diterima</label>
                                </div>
                            </div>
                            @foreach ($purchaseDetail as $pd)
                                <div class="row">
                                    <input type="hidden" name="code" value="{{ 'RD' . date('y') . $code }}">
                                    <input type="hidden" name="id_detail[]" value="{{ $pd->id }}">
                                    <input type="hidden" name="id_pn" value="{{ $purchase->id }}">
                                    <input type="hidden" name="po_id" value="{{ $pd->purchase_po_id }}">
                                    <input type="hidden" name="po_code" value="{{ $purchase->code }}">
                                    <div class="col-md-3 mt-3">
                                        <input type="text" class="form-control" name="{{ $pd->id }}_product_name"
                                            value="{{ $pd->product_name }}" readonly>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <input type="text" class="form-control" name="{{ $pd->id }}_qty"
                                            value="{{ $pd->qty }}" readonly>
                                    </div>
                                    @if ($pd->accepted_qty == null)
                                        <div class="col-md-3 mt-3">
                                            <input type="text" class="form-control"
                                                name="{{ $pd->id }}_accepted_qty" value="0" readonly>
                                        </div>
                                    @else
                                        <div class="col-md-3 mt-3">
                                            <input type="text" class="form-control"
                                                name="{{ $pd->id }}_accepted_qty"
                                                value="{{ number_format($pd->accepted_qty, 0, ',', '.') }}" readonly>
                                        </div>
                                    @endif
                                    <div class="col-md-3 mt-3">
                                        <input type="number" class="form-control" min="0"
                                            max="{{ $pd->qty - $pd->accepted_qty }}" name="{{ $pd->id }}_new_qty"
                                            value="{{ $pd->qty - $pd->accepted_qty }}" step="any">
                                        <small class="text-red">Kekurangan :
                                            {{ number_format($pd->qty - $pd->accepted_qty, 2, ',', '.') }}</small>
                                    </div>
                                </div>
                            @endforeach



                            @if ($purchaseBonus->isNotEmpty())
                                <hr>
                                <div class="row">
                                    <h2>Bonus Pembelian</h2>
                                    <div class="col-md-3  mt-4">
                                        <label class="form-label">Nama Produk Bonus</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Qty</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Qty Sudah diterima</label>
                                    </div>
                                    <div class="col-md-3 mt-4">
                                        <label class="form-label">Qty diterima</label>
                                    </div>
                                </div>

                                @foreach ($purchaseBonus as $pb)
                                    <div class="row">
                                        <input type="hidden" name="code" value="{{ 'RD' . date('y') . $code }}">
                                        <input type="hidden" name="id_detail[]" value="{{ $pb->id }}">
                                        <input type="hidden" name="id_pn" value="{{ $purchase->id }}">
                                        <div class="col-md-3 mt-3">
                                            <input type="text" class="form-control"
                                                name="{{ $pb->id }}_product_name" value="{{ $pb->product_name }}"
                                                readonly>
                                        </div>
                                        <div class="col-md-3 mt-3">
                                            <input type="text" class="form-control" name="{{ $pb->id }}_qty"
                                                value="{{ $pb->qty }}" readonly>
                                        </div>
                                        @if ($pb->accepted_qty == null)
                                            <div class="col-md-3 mt-3">
                                                <input type="text" class="form-control"
                                                    name="{{ $pb->id }}_accepted_qty" value="0" readonly>
                                            </div>
                                        @else
                                            <div class="col-md-3 mt-3">
                                                <input type="text" class="form-control"
                                                    name="{{ $pb->id }}_accepted_qty"
                                                    value="{{ number_format($pb->accepted_qty, 0, ',', '.') }}" readonly>
                                            </div>
                                        @endif
                                        <div class="col-md-3 mt-3">
                                            <input type="number" class="form-control" min="0"
                                                max="{{ $pb->qty - $pb->accepted_qty }}"
                                                name="{{ $pb->id }}_new_qty"
                                                value="{{ $pb->qty - $pb->accepted_qty }}">
                                            <small class="text-red">Kekurangan :
                                                {{ number_format($pb->qty - $pb->accepted_qty, 0, ',', '.') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    // Format angka ke format ribuan Indonesia
    function formatToRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('nominal_payment');
        const btnPas = document.getElementById('btnNominalPas');

        if (input) {
            input.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value ? formatToRupiah(value) : '';
            });
        }

        if (btnPas && input) {
            btnPas.addEventListener('click', function () {
                let underPayment = @json($underPayment);
                input.value = formatToRupiah(underPayment);
            });
        }
    });
</script>

@endpush