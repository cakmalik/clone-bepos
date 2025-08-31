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
                    <h2 class="page-title text-uppercase">
                        Journal Closing
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif
            @foreach ($errors->all() as $error)
                <x-alert level="danger" message="{{ $error }}" />
            @endforeach --}}

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <form action="{{ route('journal_closing.store') }}" method="POST">
                        @csrf
                        <div class="card-header">
                            <h3 class="card-title">Journal Closing</h3>
                        </div>
                        <div class="card-body border-bottom py-3">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Nama Periode Closing</label>
                                    <input id="name" type="text" name="name" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input id="start-date" type="date" name="start_date" class="form-control date-filter"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input id="end-date" type="date" name="end_date" class="form-control date-filter"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="submit">
                                        Simpan
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="journal-closing-table"
                                            class="table card-table yajra-datatable table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nomor Jurnal</th>
                                                    <th>Tipe Jurnal</th>
                                                    <th>Tanggal</th>
                                                    <th>Akun</th>
                                                    <th>Keterangan</th>
                                                    <th>Debit</th>
                                                    <th>Kredit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            var table = $('#journal-closing-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ url('/accounting/journal_closing') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'startDate': $('#start-date').val(),
                            'endDate': $('#end-date').val(),
                        });
                    }
                },
                columns: [{
                        data: item => item.journal_number.code,
                        name: 'code'
                    },
                    {
                        data: item => item.journal_number.journal_type.name,
                        name: 'journal_type_name'
                    },
                    {
                        data: item => item.journal_number.date,
                        name: 'journal_number_date'
                    },
                    {
                        data: item => item.journal_account.name,
                        name: 'journal_account_name'
                    },
                    {
                        data: (item) => `
                        <div><strong>Outlet: ${item.journal_number.outlet?.name || '-'}</strong></div>
                        <div>${item.description || ''}</div>
                    `,
                        name: 'description'
                    },
                    {
                        data: item => item.type === 'debit' ? generateCurrency(item.nominal) : null,
                        name: 'debit'
                    },
                    {
                        data: item => item.type === 'credit' ? generateCurrency(item.nominal) : null,
                        name: 'credit'
                    },
                ]
            })
            $('.date-filter').on('change', function() {
                table.ajax.reload();
            })
        })
    </script>
@endpush
