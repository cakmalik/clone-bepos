<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#retur-sales">
            <i class="fas fa-search me-2"></i>
            Penjualan
        </a>
    </div>
    <div class="modal modal-blur fade" id="retur-sales" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Data Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="salesTable"
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Total</th>
                                    <th>Customer</th>
                                    <th>Kasir</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $s)
                                    <tr>
                                        <td>
                                            {{ $s->sale_code }}
                                        </td>
                                        <td>
                                            {{ dateWithTime($s->sale_date) }}
                                        </td>
                                        <td>
                                            {{ currency($s->final_amount) }}
                                        </td>
                                        <td>{{ $s->customer->name }}</td>
                                        <td>{{ optional($s->user)->users_name }}</td>
                                        {{-- <td style="text-align: center;">
                                            <input type="radio" class="form-check-input" name="sales_id"
                                                data-id="{{ $s->id }} " id="retur_sales_id">
                                        </td> --}}
                                        <td style="text-align: center;">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm pilih-button rounded-2 px-3"
                                                data-id="{{ $s->id }}">
                                                <i class="fas fa-check me-1 icon-check" style="display: none;"></i>
                                                <span class="button-text">Pilih</span>
                                            </button>
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
