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
                        Stock Mutation Inventory to Outlet
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="form-group text-end">
                    <a href="{{ route('stockMutationInventoryToOutlet.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Mutasi Baru
                    </a>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Stok Mutasi Gudang ke Outlet</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Mulai</label>
                                <input id="filter-start-date" type="date" name="start_date"
                                    class="form-control filter-stock-mutation" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Sampai</label>
                                <input id="filter-end-date" type="date" name="end_date"
                                    class="form-control filter-stock-mutation" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Jenis</label>
                                <select id="filter-type" class="form-control filter-stock-mutation" name="type">
                                    <option value="" selected>SEMUA</option>
                                    <option value="outgoing">KELUAR</option>
                                    <option value="incoming">MASUK</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Status</label>
                                <select id="filter-status" class="form-control filter-stock-mutation" name="status">
                                    <option value="" selected>SEMUA</option>
                                    <option value="draft">DRAFT</option>
                                    <option value="open">OPEN</option>
                                    <option value="done">SELESAI</option>
                                    <option value="void">VOID</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="stock-mutation-table"
                                        class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kode Mutasi</th>
                                                <th>Tanggal</th>
                                                <th>Dari</th>
                                                <th>Ke</th>
                                                <th>Status</th>
                                                <th></th>
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
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let table
        $(document).ready(function() {
            table = $('#stock-mutation-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                order: false,
                ajax: {
                    url: '{{ route('stockMutationInventoryToOutlet.index') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'start_date': $('#filter-start-date').val(),
                            'end_date': $('#filter-end-date').val(),
                            'type': $('#filter-type').val(),
                            'status': $('#filter-status').val(),
                        });
                    },
                },
                columns: [{
                        data: row => `<a href="{!! url('stock_mutation_inventory_to_outlet/print') !!}/${row.id}">${row.code}</a>`,
                        name: 'code'
                    },
                    {
                        data: row => moment(row.date).format('D MMMM Y hh:mm'),
                        name: 'date'
                    },
                    {
                        data: row => row.inventory_source.name,
                        name: 'inventory_source_id'
                    },
                    {
                        data: row => row.outlet_destination.name,
                        name: 'outlet_destination_id'
                    },
                    {
                        data: row => {
                            switch (row.status) {
                                case 'draft':
                                    return `<span class="badge bg-warning">Draft</span>`;
                                case 'open':
                                    return `<span class="badge bg-primary">Open</span>`;
                                case 'done':
                                    return `<span class="badge bg-success">Selesai</span>`;
                                case 'void':
                                    return `<span class="badge bg-danger">Void</span>`;
                                default:
                                    return null;
                            }
                        },
                        name: 'status'
                    },
                    {
                        data: row => `<a href="{{ url('stock_mutation_inventory_to_outlet') }}/print/${row.id}">
                            <i class="fa fa-print"></i>
                        </a>`,
                        name: 'action'
                    }
                ]
            })

            $('.filter-stock-mutation').each(function() {
                $(this).on('change', function() {
                    table.ajax.reload();
                })
            })
        })
    </script>
@endpush
