@extends('layouts.app')
@section('page')

<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="page-title mb-0">Pelanggan Teratas</h2>
                    <small class="text-muted d-block" id="salesStatistic"></small>
                </div>
                <div class="card bg-white mb-0"
                    id="dateRangeTrigger"
                    style="border-radius: 0.5rem; min-width: 250px; cursor: pointer;">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-calendar-days me-2 text-primary"></i>
                        <span class="text-dark fw-bold" id="dateRangeText"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="card py-3" style="border-radius: 1rem">
                <div class="card-body py-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="topCustomerTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">#</th>
                                    <th>Nama Pelanggan</th>
                                    <th class="text-center" width="15%">Jumlah Transaksi</th>
                                    <th class="text-center" width="10%">Qty Beli</th>
                                    <th class="text-end" width="15%">Total Pembelian</th>
                                    <th class="text-center" width="15%">Terakhir Transaksi</th>
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

@push('js')
    <script>
        let table = null;

        $(function() {
            const $dateRangeTrigger = $('#dateRangeTrigger');
            const $dateRangeText = $('#dateRangeText');
            const $salesStatistic = $('#salesStatistic');

            const start = moment().startOf('month');
            const end = moment().endOf('month');

            const formatter = new Intl.NumberFormat('id-ID');
            const currencyFormatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            });

            if ($.fn.DataTable.isDataTable('#topCustomerTable')) {
                $('#topCustomerTable').DataTable().clear().destroy();
            }

            table = $('#topCustomerTable').DataTable({
                pageLength: 25,
                info: true,
                searching: true,
                lengthChange: true,
                columns: [
                    { title: "#", className: "text-center" },
                    { title: "Nama Pelanggan" },
                    { title: "Jumlah Transaksi", className: "text-center" },
                    { title: "Qty Beli", className: "text-end" },
                    { title: "Total Pembelian", className: "text-end" },
                    { title: "Terakhir Transaksi", className: "text-center" },
                ],

                data: [],
            });

            function fetchDashboardData(startDate, endDate) {
                $dateRangeText.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));
                $salesStatistic.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));

                $.ajax({
                    url: '/dashboard/top-customer-data',
                    method: 'GET',
                    data: {
                        start_date: startDate.format('YYYY-MM-DD'),
                        end_date: endDate.format('YYYY-MM-DD')
                    },
                    success: function(response) {
                        table.clear();

                        if (response.topCustomers.length === 0) {
                            table.row.add([
                                '',
                                '',
                                '<div class="text-center text-muted">Tidak ada data</div>',
                                '',
                                '',
                                ''
                            ]);
                        } else {
                            response.topCustomers.forEach((customer, index) => {
                                table.row.add([
                                    index + 1,
                                    customer.customer_name,
                                    customer.transaction_count,
                                    formatter.format(customer.total_qty) + ' ' + customer.unit_symbol,
                                    currencyFormatter.format(customer.total_amount),
                                    customer.last_sold ? moment(customer.last_sold).format('DD-MM-YYYY') : '-'
                                ]);
                            });
                        }

                        table.draw();
                    }
                })
            }

            $dateRangeTrigger.daterangepicker({
                opens: 'left',
                locale: {
                    format: 'DD MMM YYYY',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    customRangeLabel: 'Rentang Kustom',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                },
                startDate: start,
                endDate: end,
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, function(start, end) {
                fetchDashboardData(start, end);
            });

            fetchDashboardData(start, end);
        })
    </script>
@endpush