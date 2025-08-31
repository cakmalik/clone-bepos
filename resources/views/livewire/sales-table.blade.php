<div class="card">
    <div class="col m-3 border-bottom border-secondary">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title text-uppercase fw-bold">Penjualan</h3>
            <h3 id="summary" class="summary">Rp{{ $summary }}</h3>
        </div>
    </div>
    <div class="card-body border-bottom">
        <div class="row align-bottom">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="start">Mulai</label>
                    <input type="date" class="form-control" name="start_date" id="start_date"
                        wire:model="start_date">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="end">Sampai</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" wire:model="end_date">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="outlet">Outlet</label>
                    <select name="outlet" id="outlet" class="form-select" wire:model="outlet"
                        wire:change="outletChanged">
                        <option value="-">-- Pilih Outlet --</option>
                        @foreach ($outlets as $o)
                            <option value="{{ $o->id }}">{{ $o->name }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">

                    <label for="outlet">Kasir</label>
                    <select name="outlet" id="outlet" class="form-select"
                        wire:change="userChanged($event.target.value)">
                        <option value="-">-- Pilih Kasir --</option>
                        @foreach ($cashier as $kas)
                            <option value="{{ $kas['id'] }}">{{ $kas['name'] }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
            <div class="col-md-2 ">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-select" wire:model="status">
                        <option value="" selected>-- Semua --</option>
                        <option value="success">SUKSES</option>
                        <option value="draft">DRAFT</option>
                        <option value="void">VOID</option>
                        <option value="retur">RETUR</option>
                    </select>
                </div>
            </div>

            @if ($is_shipping && config('app.current_version_product') == 'retail_advance')
                <div class="col-md-2 mb-3">
                    <div class="form-group">
                        <label for="status">Shipping Status</label>
                        <select name="status" id="status" class="form-select" wire:model="shipping_status">
                            <option value="" selected>-- Semua --</option>
                            @foreach (\App\Enums\ShippingStatus::cases() as $item)
                                <option value="{{ $item }}">{{ $item->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="col-md mt-auto">
                <div class="d-flex justify-content-start gap-1">
                    <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Export
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" wire:click="export('excel')">Excel</a></li>
                            <li><a class="dropdown-item" wire:click="export('pdf')">Pdf</a>
                            </li>
                        </ul>
                    </div>
                    <button wire:click="resett()" type="button" class="btn btn-danger btn-block">Reset</button>
                </div>
            </div>
            <div class="col-md-2 mt-auto">
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table no-datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Id transaksi</th>
                        <th>Tanggal</th>
                        <th>outlet</th>
                        <th>customer</th>
                        <th>Total transaksi</th>
                        <th>Status</th>
                        @if ($is_shipping && config('app.current_version_product') == 'retail_advance')
                            <th>Status Pengiriman</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $key => $i)
                        <tr @if ($i->is_retur) style="background-color: #f8d7da" @endif>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="sales/{{ $i->sale_code }}" data-toggle="tooltip" data-id="{{ $i->id }}"
                                    data-original-title="Show">{{ $i->sale_code }}</a>

                            </td>
                            <td>{{ Carbon\Carbon::parse($i->sale_date)->translatedFormat('d/m/Y H:i') }}</td>
                            <td>{{ $i->outlet->name }}</td>
                            <td>
                                <a href="" id="detail_customer" data-bs-toggle="modal"
                                    data-bs-target="#modal_detail_customer" data-name="{{ $i->customer->name }}"
                                    data-code="{{ $i->customer->code }}" data-phone="{{ $i->customer->phone }}"
                                    data-address="{{ $i->customer->address }}">{{ $i->customer->name }}</a>
                            </td>
                            <td style="text-align: right">

                                @if ($i->is_retur)
                                    -
                                @endif
                                {{ rupiah($i->final_amount) }}
                            </td>
                            <td>
                                @php
                                    $badgeClass = '';
                                    $statusText = '';
                            
                                    if ($i->ref_code != null && $i->status == 'success') {
                                        $badgeClass = 'bg-orange-lt';
                                        $statusText = 'RETUR';
                                    } elseif ($i->status == 'success') {
                                        $badgeClass = 'bg-green-lt';
                                        $statusText = 'SUKSES';
                                    } elseif ($i->status == 'draft') {
                                        $badgeClass = 'bg-blue-lt';
                                        $statusText = 'DRAFT';
                                    } elseif ($i->status == 'void') {
                                        $badgeClass = 'bg-red-lt';
                                        $statusText = 'VOID';
                                    }
                                @endphp
                            
                                <span class="badge text-uppercase badge-sm {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            
                            @if ($is_shipping && config('app.current_version_product') == 'retail_advance')
                                <td>
                                    @php
                                        $btnBg = 'btn-outline-warning';

                                        if ($i->shipping_status == 'in_transit') {
                                            $btnBg = 'btn-outline-secondary';
                                        } elseif ($i->shipping_status == 'out_for_delivery') {
                                            $btnBg = 'btn-outline-primary';
                                        } elseif ($i->shipping_status == 'delivered') {
                                            $btnBg = 'btn-outline-success';
                                        }

                                        $shippingIcon = 'fa-clock';

                                        if ($i->shipping_status == 'in_transit') {
                                            $shippingIcon = 'fa-truck';
                                        } elseif ($i->shipping_status == 'out_for_delivery') {
                                            $shippingIcon = 'fa-shipping-fast';
                                        } elseif ($i->shipping_status == 'delivered') {
                                            $shippingIcon = 'fa-check-circle';
                                        }

                                    @endphp
                                    <a href="#"
                                        class="btn btn-sm {{ $btnBg }} text-decoration-none rounded"
                                        id="detail_shipping" data-shipping="{{ $i->id }}"
                                        data-current-status="{{ $i->shipping_status }}">
                                        <li class="fas {{ $shippingIcon }} me-1" aria-hidden="true"></li>
                                        {{ \App\Enums\ShippingStatus::from($i->shipping_status)->label() }}
                                    </a>
                                </td>
                            @endif
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary rounded" id="detail_sales"
                                    data-detail="{{ $i->id }}">
                                    <li class="fa fa-receipt me-1" aria-hidden="true"></li>Detail
                                </a>
                                @if ($i->reprint_count > 0)
                                    <a href="#" class="btn btn-sm btn-outline-primary" id="print_logs"
                                        data-detail="{{ $i->id }}">
                                        <li class="fas fa-eye me-1" aria-hidden="true"></li>Print Logs
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <x-load-more :data="$data" />

        </div>
    </div>
</div>
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

    $(document).on('click', '#print_logs', function() {
        document.getElementById('mdlDetail').innerHTML = ''
        var id = $(this).data('detail')
        var salesDetails = [];
        $.ajax({
            type: "GET",
            url: '/sales/' + id + '/print-logs',
            success: function(result) {
                console.log(result.data, 'result')
                // reset first
                document.getElementById('mdlDetail').innerHTML = ''
                data = result.data
                buildTableLog(data)
                $('#modal_print_logs').modal('show');
            },
            error: function(result) {
                alert('error');
            }
        });
    });

    $(document).on('click', '#detail_customer', function() {
        var judul = $(this).data('judul');
        var name = $(this).data('name');
        var code = $(this).data('code');
        var phone = $(this).data('phone');
        var address = $(this).data('address');
        $('#judul').text(judul);
        $('#name').val(name);
        $('#code').val(code);
        $('#phone').val(phone);
        $('#address').val(address);
    });

    $(document).ready(function() {
        var selectedShippingId;


        $(document).on('click', '#detail_shipping', function(e) {
            e.preventDefault();
            selectedShippingId = $(this).data('shipping');
            var currentStatus = $(this).data(
                'current-status'); // Ambil status saat ini dari atribut data

            // Reset semua card yang terpilih sebelumnya
            $('.status-card').removeClass('selected');
            $('input[name="shipping_status"]').prop('checked', false);

            // Setel radio button yang sesuai dengan status saat ini
            $('input[name="shipping_status"]').each(function() {
                if ($(this).val() === currentStatus) {
                    $(this).prop('checked', true);
                    $(this).closest('.status-card').addClass(
                        'selected'); // Tambahkan class untuk styling
                }
            });


            $('#modal_detail_shipping').modal('show');
        });

        $('input[name="shipping_status"]').on('change', function() {
            $('.status-card').removeClass('selected');
            $(this).closest('.status-card').addClass('selected');
        });


        $(document).on('click', '#saveStatus', function() {
            var selectedStatus = $('input[name="shipping_status"]:checked').val();

            $.ajax({
                url: '/update-shipping-status', // Ganti dengan URL endpoint Anda
                method: 'POST',
                data: {
                    shipping_id: selectedShippingId,
                    status: selectedStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#statusModal').modal('hide'); // Tutup modal

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status pengiriman berhasil diperbarui.',
                            confirmButtonText: 'OK',
                            timer: 2000, // Alert akan menutup otomatis setelah 3 detik
                            timerProgressBar: true
                        }).then(() => {
                            location
                                .reload(); // Reload halaman setelah menutup alert
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memperbarui status.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    $('#statusModal').modal('hide'); // Tutup modal
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan pada server.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    });


    function formatCurrency(amount) {
        const options = {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        };

        return amount.toLocaleString('id-ID', options);
    }

    function buildTable(data) {
        var table = document.getElementById('mdlDetail');
        let totalPenjualan = 0;
        let totalRetur = 0;

        for (var i = 0; i < data.length; i++) {
            let finalPrice = parseFloat(data[i].final_price) || 0;
            let qty = parseFloat(data[i].qty) || 0; // Pastikan qty dalam bentuk angka
            let totalPrice = finalPrice * qty;

            if (data[i].is_item_bundle == 1) {
                var row = `<tr>
                        <td class="text-nowrap text-start" style="padding-left: 100px"><small>${data[i].product_name} (${qty + ' ' + data[i].unit_symbol ?? ''})</small></td>
                            
                        </tr>`
            } else {
                var row = `<tr>
                            <td>${data[i].product_name}</td>
                            <td class="text-nowrap text-end">${qty + ' ' + data[i].unit_symbol ?? ''}  </td>
                            <td class="text-end">${formatCurrency(finalPrice)}</td>
                            <td class="text-end">${data[i].discount}</td>
                            <td style="text-align: right">${formatCurrency(totalPrice)} ${data[i].is_retur==1 ? `(Retur ${formatCurrency(data[i].qty_retur * data[i].final_price)})` : ''}</td>
                        </tr>`
            }

            table.innerHTML += row;
        }
    }
    // reset first
    document.getElementById('mdlDetail').innerHTML = ''

    function buildTableLog(data) {
        document.getElementById('mdlDetailLog').innerHTML = ''
        var table = document.getElementById('mdlDetailLog')
        for (var i = 0; i < data.length; i++) {
            var row = `<tr>
                            <td>${data[i].user}</td>
                            <td>${data[i].reprint_time}</td>
                        </tr>`
            table.innerHTML += row;
        }
    }

    $(document).ready(function() {
        var today = new Date().toISOString().slice(0, 10);
        $("#start_date").val(today);
        $("#end_date").val(today);
    });
</script>
<x-modal id="modal_detail_customer">
    <x-slot name="title">
        Detail Customer
    </x-slot>
    <x-slot name="body">
        <div class="form-group mb-2">
            <label class="label">Nama</label>
            <input id="name" class="form-control" disabled></input>
        </div>
        <div class="form-group mb-2">
            <label class="label">Code</label>
            <input id="code" class="form-control" disabled></input>
        </div>
        <div class="form-group mb-2">
            <label class="label">Hp</label>
            <input id="phone" class="form-control" disabled></input>
        </div>
        <div class="form-group mb-2">
            <label class="label">Alamat</label>
            <textarea id="address" class="form-control" disabled></textarea>
        </div>
    </x-slot>
    <x-slot name="footer">
        <!-- Footer of the modal -->
    </x-slot>
</x-modal>
<x-modal id="modal_detail_sales" size="xl">
    <x-slot name="title">
        Detail Penjualan
    </x-slot>
    <x-slot name="body">
        <table class="custom-table table-striped text-capitalize table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Produk</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Diskon</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody id="mdlDetail">
                <!-- Data will be injected here dynamically -->
            </tbody>
        </table>

    </x-slot>
    <x-slot name="footer">
        <!-- Footer of the modal -->
    </x-slot>
</x-modal>

<x-modal id="modal_print_logs" size="modal-xl">
    <x-slot name="title">
        Print Logs
    </x-slot>
    <x-slot name="body">
        <table class="custom-table text-capitalize">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody id="mdlDetailLog">
            </tbody>
        </table>
    </x-slot>
    <x-slot name="footer">
        <!-- Footer of the modal -->
    </x-slot>
</x-modal>


<x-modal id="modal_detail_shipping">
    <x-slot name="title">
        Ubah Status Pengiriman
    </x-slot>
    <x-slot name="body">
        <form id="statusForm">
            <div class="row">
                @foreach (\App\Enums\ShippingStatus::cases() as $status)
                    <div class="col-md-6 mb-3">
                        <div class="card status-card">
                            <input type="radio" name="shipping_status" id="status{{ $status->value }}"
                                value="{{ $status->value }}" class="d-none"> <!-- Tandai status yang aktif -->
                            <label for="status{{ $status->value }}" class="card-body text-center">
                                <div class="status-icon mb-2">
                                    @if ($status->value === 'pending')
                                        <i class="fas fa-clock fa-3x text-warning"></i>
                                    @elseif($status->value === 'in_transit')
                                        <i class="fas fa-truck fa-3x text-secondary"></i>
                                    @elseif($status->value === 'out_for_delivery')
                                        <i class="fas fa-shipping-fast fa-3x text-primary"></i>
                                    @elseif($status->value === 'delivered')
                                        <i class="fas fa-check-circle fa-3x text-success"></i>
                                    @endif
                                </div>
                                <h5 class="card-title">{{ $status->label() }}</h5>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </x-slot>
    <x-slot name="footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="saveStatus">Simpan Perubahan</button>
    </x-slot>
</x-modal>
