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
                        Permintaan Pembelian
                    </h2>
                    <a href="/purchase_requisition/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Permintaan (PR)
                    </a>
                </div>

            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @include('pages.product.product_price.create') --}}
                
                <div class="card py-3">
                    <div class="card-body py-3">
                        <form method="GET" action="{{ route('purchase_requisition.index') }}" id="filter-form">
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start">Mulai</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date"
                                            value="{{ request('start_date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end">Sampai</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date"
                                            value="{{ request('end_date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="">-- SEMUA --</option>
                                            {{-- <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>OPEN</option> --}}
                                            <option value="Finish" {{ request('status') == 'Finish' ? 'selected' : '' }}>SELESAI</option>
                                            <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>DRAFT</option>
                                            <option value="Void" {{ request('status') == 'Void' ? 'selected' : '' }}>BATAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Kode PR</th>
                                        <th>Tanggal</th>
                                        <th>Dibuat</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataPurchase as $dp)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $dp->code }}</td>
                                            <td>{{ $dp->formatted_purchase_date }}</td>
                                            <td>{{ $dp->user?->users_name }}</td>
                                            <td>
                                                @if ($dp->purchase_status == 'Draft')
                                                    <span class="badge badge-sm text-uppercase bg-orange-lt">Draft</span>
                                                @elseif($dp->purchase_status == 'Open')
                                                    <span class="badge badge-sm text-uppercase bg-blue-lt">Open</span>
                                                @elseif($dp->purchase_status == 'Finish')
                                                    <span class="badge badge-sm text-uppercase bg-green-lt">Selesai</span>
                                                @elseif($dp->purchase_status == 'Void')
                                                    <span class="badge badge-sm text-uppercase bg-red-lt">Batal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($dp->purchase_status !== 'Void')
                                                    @if ($dp->purchase_status == 'Finish')
                                                        <a href="purchase_requisition_print/{{ $dp->id }}"
                                                            class="btn btn-outline-primary"><i class="fa fa-print"></i></a>
                                                    @elseif ($dp->purchase_status == 'Draft')
                                                        <a href="purchase_requisition/{{ $dp->id }}/edit"
                                                            class="btn btn-outline-dark">
                                                            <li class="fas fa-edit"></li>
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
    document.querySelectorAll('#filter-form input, #filter-form select').forEach(function (element) {
        element.addEventListener('change', function () {
            document.getElementById('filter-form').submit();
        });
    });
</script>

@endpush
