@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Pembayaran Piutang
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

                <div class="card mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Data Piutang</h3>
                    
                        <a href="{{ route('debtPayment.print', $sales->id) }}" class="btn btn-outline-primary d-flex align-items-center">
                            <i class="fa fa-print me-2"></i>
                            Cetak
                        </a>
                    </div>                    
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-md-3">
                                <span> ID Transaksi </span>
                                <h3>{{ $sales->sale_code }}</h3>
                            </div>

                            <div class="col-md-3">
                                <span>Pelanggan</span>
                                <h3> {{ $sales->customer->name ?? '-' }} </h3>
                            </div>

                            <div class="col-md-3">
                                <span>Tanggal Transaksi</span>
                                <h3>{{ $sales->sale_date }}</h3>
                            </div>

                            <div class="col-md-3">
                                <span>Tagihan Piutang</span>
                                <h3>Rp. {{ number_format($sales->final_amount) }}</h3>
                            </div>
                        </div>

                        <hr>

                        <h4>Riwayat Pembayaran</h4>
                        <table class="table no-datatable mb-4">
                            <thead>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Metode Pembayaran</th>
                                <th>Keterangan</th>
                                <th>User input</th>
                            </thead>
                            <tbody>
                                @foreach($salesPayment as $key => $value)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ dateWithTime($value->payment_date) }}</td>
                                    <td>{{ rupiah($value->nominal_payment) }}</td>
                                    <td>{{ $value->paymentMethod->name }}</td>
                                    <td>{{ $value->description }}</td>
                                    <td>{{ $value->user->users_name }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td colspan="2" style="text-align: right"><b>Total Pembayaran </b></td>
                                    <td colspan="4"><b>{{ rupiah($totalPayment) }}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align: right"><b>Sisa Piutang </b></td>
                                    <td colspan="4"><b>{{ rupiah($sales->final_amount - $totalPayment) }}</b></td>
                                </tr>
                            </tbody>
                        </table>


                        @if(($sales->final_amount - $totalPayment) > 0)
                        <br><br>
                        <h4>Input Pembayaran</h4>
                        <form action="{{ route('debtPayment.store') }}" method="post">
                            @csrf
                           
                            <input type="hidden" name="sales_id" value="{{ $sales->id }}">
                            
                            <div class="row">
                               
                                <div class="col-md-3">
                                    <label class="form-label">Nominal</label>
                                    <input type="text" autocomplete="off" name="nominal_payment"
                                        class="form-control @error('nominal') is-invalid @enderror" id="nominal_payment" value="" placeholder="Masukkan nominal">
                                    @error('nominal_payment')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Pembayaran</label>
                                    <select name="payment_method"
                                        class="form-control  @error('payment_method') is-invalid @enderror">
                                        <option selected value="0" disabled> &mdash; Pilih Pembayaran &mdash;</option>
                                        @foreach($paymentMethods as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Pembayaran</label>
                                    <input type="date" autocomplete="off" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}"
                                        id="payment_date">
                                    @error('due_date')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" autocomplete="off" name="description"
                                        class="form-control @error('description') is-invalid @enderror"
                                        id="description_payment" required>
                                    @error('description')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <div class="form-group text-end">
                                <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            </div>

                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>


    document.getElementById('nominal_payment').addEventListener('input', function (e) {
        let value = this.value.replace(/[^0-9]/g, '');

        if (value.length > 0) {
            value = parseInt(value, 10).toLocaleString('id-ID');
        }
        this.value = value;
    });
</script>
@endpush
