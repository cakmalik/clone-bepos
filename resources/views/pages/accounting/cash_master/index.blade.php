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
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="form-group text-end">
                <a href="/accounting/cash_master/create" class="btn btn-primary mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    BKK/BKM
                </a>
            </div>

            @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">BKK/BKM</label>
                                <select name="cash_type_filter" id="cash_type_filter" class="form-control">
                                    <option value="SEMUA" selected>SEMUA</option>
                                    <option value="KAS-MASUK">KAS-MASUK</option>
                                    <option value="KAS-KELUAR">KAS-KELUAR</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover"
                                id="cashmaster">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Kode</th>
                                        <th>Nama Item BK</th>
                                        <th>BKK/BKM</th>
                                        <th>Akun Debit</th>
                                        <th>Akun Kredit</th>
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
@endsection
@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            var table = $('#cashmaster').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,

                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.cash_type_filter = $('#cash_type_filter').val();
                    }
                },
                columns: [{
                        data: 'code',
                        name: 'code'
                    },

                    {
                        data: 'cash_type',
                        name: 'cash_type'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'journal_setting.debit_account.name',
                        name: 'journal_setting.debit_account.name'
                    },
                    {
                        data: 'journal_setting.credit_account.name',
                        name: 'journal_setting.credit_account.name'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }

                ]

            });

            $('#cash_type_filter').change(function() {
                table.draw();
            });
        });




        function cashMaster(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/accounting/cash_master') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                location.reload();
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                location.reload();
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
