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
                        Pembayaran Piutang
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
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Dari</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ date('Y-01-01') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">Sampai</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="payment_status" class="form-label">Status Pembayaran</label>
                                    <select name="payment_status" id="payment_status" class="form-select">
                                        <option value="">-- Semua Status --</option>
                                        @foreach ($paymentStatuses as $status)
                                            <option value="{{ $status->value }}"
                                                {{ request('payment_status') == $status->value ? 'selected' : '' }}>
                                                {{ $status->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer" class="form-label">Pelanggan</label>
                                    <select name="customer" id="customer" class="form-select">
                                        <option value="" selected>-- Semua Pelanggan --</option>
                                        @foreach ($customers as $key => $value)
                                            <option value="{{ $value->id }}"
                                                {{ request('customer') == $value->id ? 'selected' : '' }}>
                                                {{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="customer" class="form-label">&nbsp;</label>
                                    <button class="btn btn-primary" id="filter"><i class="fa fa-refresh"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">Tanggal Transaksi</th>
                                        <th style="width: 10%">ID Transaksi</th>
                                        <th style="width: 15%">Pelanggan</th>
                                        <th style="width: 15%">Total Piutang</th>
                                        <th style="width: 15%">Terbayar</th>
                                        <th style="width: 15%">Sisa</th>
                                        <th style="width: 15%">Jatuh Tempo</th>
                                        <th style="width: 10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $i)
                                        <tr>
                                            <td>{{ $i->sale_date }}</td>
                                            <td><a href="{{ url('sales/' . $i->sale_code) }}" target="__blank">
                                                    {{ $i->sale_code }}</a></td>
                                            <td>{{ $i->customer ?? '-' }}</td>
                                            <td>{{ rupiah($i->final_amount) }}</td>
                                            <td>{{ rupiah($i->total_payment) }}</td>
                                            <td>{{ $i->receivable_idr }}</td>
                                            <td class="due-date-cell" data-sale-id="{{ $i->id }}"
                                                data-due-date="{{ $i->due_date }}">
                                                <a href="#">{{ $i->due_date_formatted }}</a>
                                            </td>
                                            <td>
                                                @if ($i->receivable > 0)
                                                    <a href="{{ route('debtPayment.create', $i->id) }}"
                                                        class="btn btn-sm btn-primary rounded">
                                                        Bayar
                                                    </a>
                                                @endif

                                                {{-- cetak --}}
                                                <a href="{{ route('debtPayment.print', $i->id) }}"
                                                    class="btn btn-sm btn-outline-primary rounded">
                                                    <i class="fa fa-print me-2"></i>Cetak
                                                </a>
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
    <!-- Modal untuk Update Due Date -->
    <div class="modal fade" id="dueDateModal" tabindex="-1" aria-labelledby="dueDateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dueDateModalLabel">Atur Tanggal Jatuh Tempo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateDueDateForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.due-date-cell').forEach(function(cell) {
                cell.addEventListener('click', function() {
                    const saleId = this.getAttribute('data-sale-id');
                    const currentDueDate = this.getAttribute('data-due-date');

                    const form = document.getElementById('updateDueDateForm');
                    form.action = `/sales/${saleId}/update-due-date`;
                    console.log(currentDueDate);
                    document.getElementById('due_date').value = currentDueDate;

                    const modal = new bootstrap.Modal(document.getElementById('dueDateModal'));
                    modal.show();
                });
            });
        });

        $(document).ready(function() {
            $('.table').DataTable({
                destroy: true,
                ordering: false
            })

        });


        $(document).ready(function() {
            $('#filter').on('click', function() {
                var startDate = $('input[name="start_date"]').val();
                var endDate = $('input[name="end_date"]').val();
                var payment_status = $('#payment_status').val();
                var customer = $('#customer').val();

                var url = "{{ route('debtPayment.index') }}";
                var params = {
                    start_date: startDate,
                    end_date: endDate,
                    payment_status: payment_status,
                    customer: customer
                };

                window.location.href = url + '?' + $.param(params);
            });
        });
    </script>
@endpush
