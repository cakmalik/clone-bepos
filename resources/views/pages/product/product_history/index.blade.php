@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Riwayat Stok
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

                <div class="card py-3">
                    <div class="card-header">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="col-auto mb-2">
                                    <label class="form-label">Mulai</label>
                                    <input id="start-date" type="date" class="form-control date-filter"
                                        value="{{ date('Y-m-d') }}">
                                </div>

                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="col-auto">
                                    <label class="form-label">Sampai</label>
                                    <input id="end-date" type="date" class="form-control date-filter"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Outlet</label>
                                <select id="outlet-id" class="form-select history-stock-filter">
                                    <option selected value=""> &mdash; Pilih Outlet
                                        &mdash;
                                    </option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Gudang</label>
                                <select id="inventory-id" class="form-select history-stock-filter">
                                    <option selected value=""> &mdash; Pilih Gudang
                                        &mdash;
                                    </option>
                                    @foreach ($inventory as $inven)
                                        <option value="{{ $inven->id }}">{{ $inven->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Produk</label>
                                    <select id="product-id" class="form-select history-stock-filter">
                                        <option selected value="0" disabled> &mdash; Pilih Produk
                                            &mdash;
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2">
                                <strong>Satuan :</strong> <span id="unit-info">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table id="history-stock-table"
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>User</th>
                                        <th>Gudang/Outlet</th>
                                        <th>Awal</th>
                                        <th>Masuk</th>
                                        <th>Keluar</th>
                                        <th>Sisa</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#product-id').select2({
            ajax: {
                url: '/search-product', // Your server-side search endpoint
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // Search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Minimum characters before a search is performed
        });


    // Ketika produk dipilih
    $('#product-id').on('select2:select', function(e) {
        var data = e.params.data; // Data produk yang dipilih dari Select2
        $('#unit-info').text(data.unit); // Menampilkan unit di tempat yang diinginkan
    });

        $(function() {
            let inventoryId;
            let outletId;

            var table = $('#history-stock-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                order: false,
                pageLength: 25,
                ajax: {
                    url: '{{ url('/stock_history') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'product_id': $('#product-id').val(),
                            'outlet_id': $('#outlet-id').val(),
                            'inventory_id': $('#inventory-id').val(),
                            'startDate': $('#start-date').val(),
                            'endDate': $('#end-date').val(),
                        });
                    }
                },
                columns: [{
                        data: row => moment(row.history_date).format('D MMMM Y HH:mm'),
                        name: 'history_date'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'inventory',
                        name: 'inventory'
                    },
                    {
                        data: 'stock_before',
                        name: 'stock_before'
                    },
                    {
                        data: row => row.action_type === 'plus' ? row.stock_change : '',
                        searchable: false,
                        name: 'stock_change'
                    },
                    {
                        data: row => row.action_type === 'minus' ? row.stock_change : '',
                        searchable: false,
                        name: 'stock_change'
                    },
                    {
                        data: 'stock_after',
                        name: 'stock_after'
                    },
                    {
                        data: 'desc',
                        name: 'desc'
                    },
                ]
            })
            $('.history-stock-filter').on('change', function() {
                table.ajax.reload();
            })

            $('.date-filter').on('change', function() {
                table.ajax.reload();
            })

            $('#inventory-id').on('change', function() {
                if ($(this).val() !== '') {
                    $('#outlet-id').prop('disabled', true).val('');
                } else {
                    $('#outlet-id').prop('disabled', false);
                }
                table.ajax.reload();
            });

            $('#outlet-id').on('change', function() {
                if ($(this).val() !== '') {
                    $('#inventory-id').prop('disabled', true).val('');
                } else {
                    $('#inventory-id').prop('disabled', false);
                }
                table.ajax.reload();
            });
        })
    </script>
@endpush
