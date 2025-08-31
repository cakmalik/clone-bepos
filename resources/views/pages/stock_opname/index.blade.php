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
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @include('product.product_all.create') --}}
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('stockOpname.add') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Stock Opname Baru
                        </a>
                    </div>
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
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="selesai" selected>SELESAI</option>
                                        <option value="belum_selesai">BELUM SELESAI</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start">Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end">Sampai</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                id="table-opname">
                                <thead>
                                    <tr>

                                        <th>Kode Opname</th>
                                        <th>Tanggal</th>
                                        <th>Nama Gudang</th>
                                        <th>Nama Outlet</th>
                                        <th>Status Opname</th>
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
        $(function() {
            var table = $('#table-opname').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [{
                        data: 'opname_code',
                        name: 'opname_code'
                    },
                    {
                        data: 'so_date',
                        name: 'so_date',
                        // render: function(data, type, row) {
                        //     return moment(data).format('DD MMMM YYYY HH:mm');
                        // }
                    },
                    {
                        data: 'inventory',
                        name: 'inventory'
                    },
                    {
                        data: 'outlet',
                        name: 'outlet'
                    },

                    {
                        data: 'status',
                        name: 'status'
                    },

                ]
            });

            $('#status, #start_date, #end_date').change(function() {
                table.draw();
            });
        });
    </script>
@endpush
