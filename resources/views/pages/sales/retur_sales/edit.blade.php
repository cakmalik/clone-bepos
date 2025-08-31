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
                        Retur Penjualan
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
                <div class="card">
                    <div class="pt-3 pb-1 px-3">
                        <div class="row">
                            <div class="col-md-3">
                                <h5 class="text-muted">Kode Retur</h5>
                                <h3 class="font-weight-bold text-primary">{{ $sales->sale_code }}
                                </h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Tanggal Retur</h5>
                                <h3 class="font-weight-bold text-dark">{{ dateWithTime($sales->sale_date) }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">Nama Outlet</h5>
                                <h3 class="font-weight-bold text-dark">{{  $sales->outlet->name }}</h3>
                            </div>
                            <div class="col-md-3">
                                <h5 class="text-muted">ID Transaksi</h5>
                                <h3 class="font-weight-bold text-dark"> {{ $sales->ref_code }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card py-3">
                    <div class="card-body py-3">

                        <form action="{{ route('retur-sales.update', $sales->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')
                            <input type="hidden" name="id" value="{{ $sales->id }}">
                            <div class="row">
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Kode Produk</label>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Qty Retur</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Harga</label>
                                </div>
                                <div class="col-md-2 mt-4">
                                    <label class="form-label">Subtotal</label>
                                </div>
                            </div>
                            <div class="row">
                                @foreach ($sales->salesDetails as $s)
                                    <?php
                                    $salesdetailOri = \App\Models\SalesDetail::where('product_id', $s->product_id)->where('sales_id', $sales->sales_parent_id)->first();
                                    
                                    $_qty_ori = $salesdetailOri->qty;
                                    $qty_max_retur = abs($_qty_ori + $s->qty);
                                    
                                    ?>

                                    <div class="col-md-2 mt-3">
                                        <input type="hidden" name="{{ $s->id }}_product_id"
                                            value="{{ $s->product_id }}">
                                        <input type="text" class="form-control"
                                            name="{{ $s->id }}_retur_code_edit" value="{{ $s->product->code }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <input type="text" class="form-control"
                                            name="{{ $s->id }}_retur_name_edit" value="{{ $s->product_name }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <input type="number" class="form-control" name="{{ $s->id }}_retur_qty"
                                            id="retur_qty_edit" data-id="{{ $s->id }}" value="{{ $s->qty }}"
                                            min="1" max="{{ $_qty_ori }}" disabled>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <input type="text" class="form-control" name="{{ $s->id }}_price"
                                            id="{{ $s->id }}_retur_price_edit" value="{{ $s->final_price }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-2 mt-3">
                                        <input type="text" class="form-control subtotal"
                                            name="{{ $s->id }}_subtotal" id="{{ $s->id }}_subtotal_edit"
                                            value="{{ $s->qty * $s->final_price }}" readonly>
                                    </div>
                                @endforeach
                            </div>


                            <div class="row mt-3">
                                <div class="col-md-10">
                                    <h2 class="text-end">Total</h2>
                                </div>
                                <div class="col-md-2">
                                    <h2 id="totalReturSales">Rp. 0</h2>
                                </div>
                            </div>

                            {{-- <div class="form-group text-end">
                                <button type="submit" class="btn btn-success mt-3"><i class="fa fa-save"></i>&nbsp;
                                    Update</button>
                            </div> --}}
                        </form>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            @include('pages.sales.retur_sales.destroy')
                            @include('pages.sales.retur_sales.finish')
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    @include('sweetalert::alert')
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            updateTotal();

            $('body').on('change', '#retur_qty_edit', function() {
                let id = $(this).data('id');
                let qty = $(this).val();
                $('#' + id + '_subtotal_edit').val(
                    parseInt(qty) * parseInt($('#' + id + '_retur_price_edit').val())
                );
                updateTotal();
            });

            $('body').on('keyup', '#retur_qty_edit', function() {
                let id = $(this).data('id');
                let qty = $(this).val();
                $('#' + id + '_subtotal_edit').val(
                    parseInt(qty) * parseInt($('#' + id + '_retur_price_edit').val())
                );
                updateTotal();
            });
        });

        function updateTotal() {
            let totalReturSales = 0;

            $('.subtotal').each(function() {

                totalReturSales += parseFloat(this.value);
            });

            $('#totalReturSales').text('Rp. ' + groupNumber(totalReturSales));
        }

        function groupNumber(number) {
            const format = number.toString().split('').reverse().join('');
            const convert = format.match(/\d{1,3}/g);
            const group = convert.join('.').split('').reverse().join('');

            return group;
        }
    </script>
@endpush
