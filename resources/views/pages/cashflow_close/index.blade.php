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
                        Tutup Buku Kas
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
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Mulai</label>
                                <input id="filter-start-date" type="date" name="start_date"
                                    class="form-control filter-cashflow" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Sampai</label>
                                <input id="filter-end-date" type="date" name="end_date"
                                    class="form-control filter-cashflow" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label">Outlet</label>
                                <select id="filter-outlet-id" class="form-control filter-cashflow select2">
                                    @if (auth()->user()->role->role_name == 'SUPERADMIN')
                                        <option value="" selected>Semua</option>
                                    @endif
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="cashflow-table"
                                        class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kasir</th>
                                                <th>Tanggal</th>
                                                <th>Outlet</th>
                                                <th>Modal</th>
                                                <th>Pemasukan</th>
                                                <th>Pengeluaran</th>
                                                <th>Total Asli</th>
                                                <th>Aksi</th>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let table
        $(document).ready(function() {
            $('#filter-outlet-id').select2();

            table = $('#cashflow-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ route('cashflowClose.index') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'start_date': $('#filter-start-date').val(),
                            'end_date': $('#filter-end-date').val(),
                            'outlet_id': $('#filter-outlet-id').val()
                        });
                    },
                },
                columns: [{
                        data: row => row.user?.users_name ?? '-',
                        name: 'user_id',
                    }, {
                        data: row => moment(row.date).format('D MMMM Y HH:mm'),
                        name: 'code',
                    },
                    {
                        data: row => row.outlet?.name ?? '-',
                        name: 'outlet_name',
                    },
                    {
                        data: row => generateCurrency(row.capital_amount),
                        name: 'capital_amount',
                        className: 'text-right'
                    },
                    {
                        data: row => generateCurrency(row.income_amount),
                        name: 'income_amount',
                        className: 'text-right'
                    },
                    {
                        data: row => generateCurrency(row.expense_amount),
                        name: 'expense_amount',
                        className: 'text-right'
                    },
                    {
                        data: row => generateCurrency(row.real_amount),
                        name: 'real_amount',
                        className: 'text-right'
                    },
                    {
                        data: row =>
                            `<a href="${row.preview_url}" class="btn btn-outline-primary">
                                <i class="fas fa-print"></i>
                            </a>`,
                        name: 'preview_url',
                    }
                ]
            })

            $('.filter-cashflow').each(function() {
                $(this).on('change', function() {
                    table.ajax.reload();
                })
            })
        })
    </script>
@endpush
