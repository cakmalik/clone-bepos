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
                        Jurnal
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="btn-list justify-content-end">
                <a href="{{ route('journal_transaction.create') }}" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Buat Jurnal
                </a>
            </div>
            <br>
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
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                            <li class="nav-item">
                                <a href="#tabs-journal-7" class="nav-link active" data-bs-toggle="tab">Jurnal</a>
                            </li>
                            <li class="nav-item">
                                <a href="#tabs-journal-print-7" class="nav-link" data-bs-toggle="tab">Cetak</a>
                            </li>

                        </ul>
                    </div>


                    <div class="card-body border-bottom py-3">

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Mulai</label>
                                <input id="start-date" type="date" class="form-control date-filter" name="start_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sampai</label>
                                <input id="end-date" type="date" class="form-control date-filter" name="end_date"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-auto">
                                <label class="form-label">Cetak</label>
                                <button class="btn btn-danger" onclick="printPageArea()"><i
                                        class="fa fa-print"></i></button>
                            </div>
                        </div>



                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-journal-7">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table id="journal-table"
                                                class="table card-table w-100 yajra-datatable table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
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

                            <div class="tab-pane" id="tabs-journal-print-7">
                                <div class="card-body border-bottom py-3">

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div id="loadingProgress"></div>
                                            <iframe id="printframe" name="printframe"
                                                style="width: 100%; height: 1500px; border:none;"></iframe>
                                        </div>
                                    </div>


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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let table;

        $(function() {
            var table = $('#journal-table').DataTable({
                destroy: true,
                processing: true,
                serverSide: true,
                searching: false,
                paging: true,
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ url('/accounting/journal_transaction') }}',
                    data: function(d) {
                        return $.extend({}, d, {
                            'startDate': $('#start-date').val(),
                            'endDate': $('#end-date').val(),
                        });
                    }
                },
                columns: [{
                        data: item => item.journal_number.journal_closing_id ? ('CLOSING-' +
                            item.journal_number.code) : item.journal_number.code,
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
                        <div>${item?.description || ' '}</div>
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
                table.draw();
            })


            let app_url = {!! json_encode(url('/')) !!};
            let start_date = $('#start-date').val();
            let end_date = $('#end-date').val();

            let urlNota = app_url + '/accounting/journal-transaction-data?start_date=' + start_date + '&end_date=' +
                end_date;

            $(function() {
                $('#printframe').attr('src', urlNota);
            });

            $('.date-filter').change(function() {
                table.ajax.reload();
                start_date = $('#start-date').val();
                end_date = $('#end-date').val();
                urlNota = app_url + '/accounting/journal-transaction-data?start_date=' + start_date +
                    '&end_date=' + end_date;
                $('#printframe').attr('src', urlNota);
            });



        })

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
