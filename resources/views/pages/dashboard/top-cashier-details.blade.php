@extends('layouts.app')
@section('page')

<div class="container-xl">
    <!-- Page title -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <h2 class="page-title mb-0">Kasir Terlaris</h2>
                    <button 
                        type="button" 
                        style="background: none; border: none; padding: 0; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;" 
                        data-bs-toggle="popover" 
                        title="Informasi" 
                        data-bs-content="Total Penjualan dan Qty Terjual berdasarkan kasir tidak dikurangi retur karena retur dapat dilakukan oleh admin maupun kasir. Hal ini menyebabkan nilai penjualan kasir belum sepenuhnya merefleksikan pengurangan akibat retur."
                        data-bs-trigger="focus"
                    >
                        <i class="fa-solid fa-info-circle" style="font-size: 14px; color: #4395ec;"></i>
                    </button>
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
                        <table class="table table-bordered table-striped" id="topCashierTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="5%">#</th>
                                    <th>Nama Kasir</th>
                                    <th class="text-center">Jumlah Transaksi</th>
                                    <th class="text-center">Total Qty</th>
                                    <th class="text-end">Total Penjualan</th>
                                    <th class="text-center">Terakhir Transaksi</th>
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

            if ($.fn.DataTable.isDataTable('#topCashierTable')) {
                $('#topCashierTable').DataTable().clear().destroy();
            }

            table = $('#topCashierTable').DataTable({
                pageLength: 25,
                info: true,
                searching: true,
                lengthChange: true,
                columns: [
                    { title: "#", className: "text-center" },
                    { title: "Nama Kasir" },
                    { title: "Jumlah Transaksi", className: "text-center" },
                    { title: "Qty Terjual", className: "text-end" },
                    { title: "Total Penjualan", className: "text-end" },
                    { title: "Terakhir Transaksi", className: "text-center" },
                ],
                
                data: [],
            });

            function fetchDashboardData(startDate, endDate) {
                $dateRangeText.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));
                $salesStatistic.text(startDate.format('DD MMM YYYY') + ' - ' + endDate.format('DD MMM YYYY'));

                $.ajax({
                    url: '/dashboard/top-cashier-data',
                    method: 'GET',
                    data: {
                        start_date: startDate.format('YYYY-MM-DD'),
                        end_date: endDate.format('YYYY-MM-DD')
                    },
                    success: function(response) {
                        table.clear();

                        if (response.cashierDetails.length === 0) {
                            table.row.add([
                                '',
                                '',
                                '<div class="text-center text-muted">Tidak ada data</div>',
                                '',
                                '',
                                ''
                            ]);
                        } else {
                            response.cashierDetails.forEach((cashier, index) => {
                                table.row.add([
                                    index + 1,
                                    cashier.cashier_name,
                                    cashier.transaction_count,
                                    formatter.format(cashier.total_qty) + ' ' + cashier.unit_symbol,
                                    currencyFormatter.format(cashier.total_sales),
                                    cashier.last_transaction ? moment(cashier.last_transaction).format('DD-MM-YYYY') : '-'
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
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
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
        });

    </script>
@endpush