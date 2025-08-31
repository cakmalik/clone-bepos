<div class="col-sm-6 col-lg-3">
    <div class="card  text-teal">
        <div class="card-status-bottom bg-teal py-1"></div>
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-uppercase text-teal">
                        Sukses
                    </div>
                    <div class="h1 mb-0 font-weight-bold text-gray-800 mt-2">
                        <i class="fa fa-check-circle text-teal me-2"></i>
                        {{ $success ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card text-dark">
        <div class="card-status-bottom bg-dark py-1"></div>
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-uppercase text-dark">
                        Draft
                    </div>
                    <div class="h1 mb-0 font-weight-bold text-gray-800 mt-2">
                        <i class="fa fa-clock text-dark me-2"></i>
                        {{ $status['draft'] ?? 0 }}
                    </div>
                </div>

            </div>
        </div>
        <div id="chart-revenue-bg" class="chart-sm"></div>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="card text-primary">
        <div class="card-status-bottom bg-primary py-1"></div>
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-uppercase text-primary">
                        Pendapatan Hari Ini
                    </div>
                    <div class="h1 mb-0 font-weight-bold text-gray-800 mt-2">
                        <i class="fa fa-coins text-primary me-2"></i>
                        Rp. {{ number_format($revenue) ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-6 col-lg-3">
    <div class="card text-warning">
        <div class="card-status-bottom bg-warning py-1"></div>
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-uppercase text-warning">
                        Transaksi</div>
                    <ul class="list-unstyled mt-1">
                        <li>
                            <div class="d-flex justify-content-between">
                                <span>Hari ini</span>
                                <span>{{ $tx['daily'] }}</span>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex justify-content-between">
                                <span>Minggu ini</span>
                                <span>{{ $tx['weekly'] }}</span>
                            </div>
                        </li>
                        <li>
                            <div class="d-flex justify-content-between">
                                <span>Bulan ini</span>
                                <span>{{ $tx['monthly'] }}</span>

                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
