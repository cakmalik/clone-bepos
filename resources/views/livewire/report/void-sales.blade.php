<div>
    <div class="d-flex align-items-end mb-3 gap-2">
        <div class="col-md-3">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" wire:model="start_date" class="form-control" id="start_date"
                    placeholder="Enter start_date">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" wire:model="end_date" class="form-control" id="end_date"
                    placeholder="Enter end_date">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <a href="{{ route('reportVoidPrint', ['start_date' => $start_date ?? now()->toDateString(), 'end_date' => $end_date ?? now()->toDateString()]) }}"
                    class="btn btn-primary" target="_blank" wire:loading.attr="disabled">CETAK</a>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table
            class="no-datatable table card-table table-vcenter text-nowrap datatable  table-bordered  table-hover">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Outlet</th>
                    <th>Kasir</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i)
                    <tr>
                        <td class="text-capitalize">{{ $i->created_at }}</td>
                        <td class="text-capitalize">{{ $i->outlet?->name }}</td>
                        <td class="text-capitalize">{{ $i->user?->users_name }}</td>
                        <td class="text-capitalize">{{ $i->product_name }}</td>
                        <td class="">Rp{{ number_format($i->final_price) }}</td>
                        <td class="">{{ number_format($i->qty) }}</td>
                        <td class="">Rp{{ number_format($i->subtotal) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $data->links() }}
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('click', '#detail_sales', function() {
            document.getElementById('mdlDetail').innerHTML = ''
            var id = $(this).data('detail')
            var salesDetails = [];
            $.ajax({
                type: "GET",
                url: '/sales/' + id + '/detail',
                success: function(result) {
                    // console.log(result.data)
                    data = result.data
                    buildTable(data)
                    $('#modal_detail_sales').modal('show');
                },
                error: function(result) {
                    alert('error');
                }
            });
        });

        function buildTable(data) {
            var table = document.getElementById('mdlDetail')
            for (var i = 0; i < data.length; i++) {
                var row = `<tr>
                            <td>${data[i].product_name}</td>
                            <td>${data[i].rp_price}</td>
                            <td>${data[i].discount}</td>
                            <td>${data[i].qty}</td>
                            <td style="text-align: right">${data[i].rp_subtotal}</td>
                        </tr>`
                table.innerHTML += row;
            }
        }
    </script>
@endpush

<x-modal id="modal_detail_sales" size="modal-xl">
    <x-slot name="title">
        Detail Penjualan
    </x-slot>
    <x-slot name="body">
        <table class="custom-table text-capitalize">
            <thead>
                <tr>
                    <th>produk</th>
                    <th>harga</th>
                    <th>discount</th>
                    <th>qty</th>
                    <th>subtotal</th>
                </tr>
            </thead>
            <tbody id="mdlDetail">
            </tbody>
        </table>
    </x-slot>
    <x-slot name="footer">
        <!-- Footer of the modal -->
    </x-slot>
</x-modal>
