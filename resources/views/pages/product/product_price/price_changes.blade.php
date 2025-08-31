<div class="modal modal-blur fade" id="notif" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informasi Perubahan Harga Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="table-responsive">
                    <table
                        class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Produk</th>
                                <th>HPP Lama</th>
                                <th>Harga Jual Lama</th>
                                <th>HPP Baru</th>
                                <th>Harga Jual Baru</th>
                                <th>Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($priceChange as $key => $pc)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ dateWithTime($pc->date) }}</td>
                                    <td>{{ $pc->product_name }}</td>
                                    <td>{{ rupiah($pc->hpp_old) }}</td>
                                    <td>{{ rupiah($pc->selling_price_old) }}</td>
                                    <td>{{ rupiah($pc->hpp) }}</td>
                                    <td>{{ rupiah($pc->selling_price) }}</td>
                                    <td>{{ $pc->user?->users_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
