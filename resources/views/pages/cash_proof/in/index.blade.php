@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Kas Masuk
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="form-group text-end">
                    <a href="{{ route('cashProofIn.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Kas Masuk
                    </a>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}"/>
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}"/>
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}"/>
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Kas Masuk</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Mulai</label>
                                <input id="filter-start-date" type="date" name="start_date"
                                       class="form-control filter-cash-proof-in"
                                       value="{{ date('Y-m-d')}}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-control-label">Sampai</label>
                                <input id="filter-end-date" type="date" name="end_date"
                                       class="form-control filter-cash-proof-in"
                                       value="{{ date('Y-m-d')}}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="cash-proof-in-table"
                                           class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                        <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Tanggal</th>
                                            <th>Referensi</th>
                                            <th>Keterangan</th>
                                            <th>Nominal</th>
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
        $(document).ready(function () {
            table = $('#cash-proof-in-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ route('cashProofIn.index') }}',
                    data: function (d) {
                        return $.extend({}, d, {
                            'start_date': $('#filter-start-date').val(),
                            'end_date': $('#filter-end-date').val(),
                        });
                    },
                },
                columns: [
                    {
                        data: row => `<a href="${row.preview_url}">${row.cash_proof.code}</a>`,
                        name: 'code',
                    },
                    {
                        data: row => moment(row.cash_proof.date).format('DD/MM/YYYY'),
                        name: 'date',
                    },
                    {
                        data: 'ref_code',
                        name: 'ref_code',
                    },
                    {
                        data: 'description',
                        name: 'description',
                    },
                    {
                        data: row => generateCurrency(row.nominal),
                        name: 'nominal',
                    }
                ]
            })

            $('.filter-cash-proof-in').each(function () {
                $(this).on('change', function () {
                    table.ajax.reload();
                })
            })
        })
    </script>
@endpush
