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
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Stok Adjustment
                    </h2>
                </div>

                <div class="col-auto">
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm rounded-2">
                        <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card py-3">
                    <div class="card-body py-3">

                        <div class="row">
                            @if ($opname->inventory_id)
                                <div class="col-md-3">
                                    <span>Kode Adjusment </span>
                                    <h3>{{ 'ADJ-' . $opname->inventory->code . '-' . date('ymd') . '-' . $code }}
                                    </h3>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <span>Kode Adjusment </span>
                                    <h3>{{ 'ADJ-' . $opname->outlet->code . '-' . date('ymd') . '-' . $code }}
                                    </h3>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <span>Kode Opname </span>
                                <h3>{{ $opname->code }}</h3>
                            </div>
                            @if ($opname->inventory_id)
                                <div class="col-md-3">
                                    <span>Nama Gudang </span>
                                    <h3>{{ Str::upper($opname->inventory->name) }}</h3>
                                </div>
                            @else
                                <div class="col-md-3">
                                    <span>Nama Outlet </span>
                                    <h3>{{ Str::upper($opname->outlet->name) }}</h3>
                                </div>
                            @endif


                            <div class="col-md-3">
                                <span>Tanggal </span>
                                <h3 id="adjutment_time"></h3>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <label class="form-label">Nama Produk</label>
                            </div>

                            <div class="col-md-3 mt-3">
                                <label class="form-label">Qty Adjustment</label>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="form-label">Harga</label>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="form-label">Total Harga</label>
                            </div>
                        </div>



                        <form action="{{ route('stockAdjustment.store') }}" method="post" class="form-detect-unsaved">
                            @csrf
                            <input type="hidden" name="id" value="{{ $opname->id }}">
                            @if ($opname->inventory_id)
                                <input type="hidden" name="ref_code"
                                    value="{{ 'ADJ-' . $opname->inventory->code . '-' . date('ymd') . '-' . $code }}">
                            @else
                                <input type="hidden" name="ref_code"
                                    value="{{ 'ADJ-' . $opname->outlet->code . '-' . date('ymd') . '-' . $code }}">
                            @endif


                            <input type="hidden" name="so_date_adjustment" id="adjutment_timeNow">
                            @if ($opname->inventory_id)
                                <input type="hidden" name="inventory_id" value="{{ $opname->inventory_id }}">
                            @else
                                <input type="hidden" name="outlet_id" value="{{ $opname->outlet_id }}">
                            @endif

                            <div class="row">
                                @foreach ($StockOpnameDetail as $sod)
                                    <input type="hidden" name="code_opd" id="stock_opname_code_opd"
                                        value="{{ $sod->code }}">
                                    <div class="col-md-3 mt-3">
                                        <input type="text" name="{{ $sod->id . '_name' }}" class="form-control"
                                            value="{{ $sod->product->name }}" readonly>
                                        <input type="hidden" name="{{ $sod->id . '_id' }}" class="form-control"
                                            value="{{ $sod->product->id }}" readonly>
                                    </div>

                                    <div class="col-md-3 mt-3">
                                        <input type="text" name="{{ $sod->id . '_qty_adjustment' }}"
                                            class="form-control" min="0" id="qty_adjustment"
                                            value="{{ $sod->qty_so - $sod->qty_system }}" data-id="{{ $sod->id }}">
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <input type="text" name="{{ $sod->id . '_hpp' }}" class="form-control"
                                            value="{{ $sod->product->capital_price }}" id="{{ $sod->id . '_hpp' }}"
                                            readonly>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <input type="text" name="{{ $sod->id . '_subtotal' }}"
                                            class="form-control subtotal"
                                            value="{{ ($sod->qty_so - $sod->qty_system) * $sod->product->capital_price }}"
                                            value="{{ ($sod->qty_so - $sod->qty_system) * $sod->product->capital_price }}"
                                            id="{{ $sod->id . '_subtotal' }}" readonly>
                                    </div>
                                @endforeach

                                <div class="row mt-3">
                                    <div class="col-md-10">
                                        <h2 class="text-end">Total</h2>
                                    </div>
                                    <div class="col-md-2">
                                        <h2 id="TotalAdjustment">Rp. 0</h2>
                                    </div>
                                </div>

                            </div>
                            <button type="submit" class="btn btn-primary mt-3 float-end">Simpan</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.setInterval(function() {
            $('#adjutment_time').html(moment().format('DD MMMM Y H:mm:ss'))
        }, 1000);
        window.setInterval(function() {
            $('#adjutment_timeNow').val(moment().format('Y-M-D H:mm:ss'))
        }, 1000);


        $(document).ready(function() {
            updateTotal();


            $('body').on('keyup', '#qty_adjustment', function() {
                let id = $(this).data('id');
                $('#' + id + '_subtotal').val((parseInt($(this).val())) * parseInt($('#' + id + '_hpp')
                    .val()));
                updateTotal();

            })


            $("#qty_adjustment").on("input", function() {
                let qty_adj = $(this).val();
                adjustment = qty_adj.replace(/[^-0-9]/g, ''); // Mengizinkan angka dan tanda minus
                $(this).val(adjustment);
                updateTotal();
            });



            function updateTotal() {
                let TotalAdjustment = 0;

                $('.subtotal').each(function() {
                    TotalAdjustment += parseFloat(this.value);
                });

                $('#TotalAdjustment').text('Rp. ' + groupNumber(TotalAdjustment));
            }

            function groupNumber(number) {


                const isNegative = number < 0;
                const absoluteNumber = Math.abs(number);
                const format = absoluteNumber.toString().split('').reverse().join('');
                const convert = format.match(/\d{1,3}/g);
                const group = convert.join('.').split('').reverse().join('');

                return isNegative ? '-' + group : group;
            }


        });
    </script>
@endpush
