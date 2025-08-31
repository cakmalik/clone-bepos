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
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        {{ $title }}
                    </h2>

                    @include('pages.stock_adjustment.create')
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

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="inventory">Gudang</label>
                                    <select name="inventory" id="inventory" class="form-select">
                                        <option value="0" disabled selected> &mdash; Pilih Gudang &mdash;
                                        </option>
                                        @foreach ($inventory as $inv)
                                            <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="outlet">Outlet</label>
                                    <select name="outlet" id="outlet" class="form-select">
                                        <option value="0" disabled selected> &mdash; Pilih Outlet &mdash;
                                        </option>
                                        @foreach ($outlet as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                id="table-adjustment">
                                <thead>
                                    <tr>
                                        <th>Kode Adjustment</th>
                                        <th>Kode Opname</th>
                                        <th>Tanggal Adjustment</th>
                                        <th>Nama Gudang</th>
                                        <th>Nama Outlet</th>
                                        <th>Status</th>
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
            var table = $('#table-adjustment').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.inventory = $('#inventory').val();
                        d.outlet = $('#outlet').val();
                    }
                },
                columns: [{
                        data: 'adjustment_code',
                        name: 'adjustment_code'
                    },
                    {
                        data: 'opname_code',
                        name: 'opname_code'
                    },
                    {
                        data: 'so_date',
                        name: 'so_date'
                    },
                    {
                        data: 'inventory',
                        name: 'inventory.name'
                    },
                    {
                        data: 'outlet',
                        name: 'outlet.name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                ]
            });

            $('#inventory, #outlet, #start_date, #end_date').change(function() {
                table.draw();
            });
        });
    </script>
@endpush
