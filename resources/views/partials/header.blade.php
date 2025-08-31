@php
    $stockMinimum = productStockForReminder();
    $dueDateInvoices = dueDateInvoice();
    $profileCompany = profileCompany();
@endphp

<header class="navbar navbar-expand-md navbar-light d-none d-lg-flex d-print-none">
    <div class="col-lg-12">
        <div class="row" style="float: right">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-nav flex-row order-md-last px-5">
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="{{ route('outlet.index') }}" class="btn btn-outline-primary btn-sm rounded">
                        @if (getOutletActive()->is_main == true)
                            <b> Main Outlet ( Outlet Utama ) : {{ getOutletActive()->name }} </b>
                        @else
                            <b> {{ getOutletActive()->name }} </b>
                        @endif
                    </a>
                </div>

                @if (checkSettingStockReminder() == 1)
                    <div class="nav-item dropdown d-none d-md-flex me-3">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                            aria-label="Show notifications">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg>

                            @if ($stockMinimum->isNotEmpty() || $dueDateInvoices->isNotEmpty())
                                <span class="badge bg-red"></span>
                            @endif

                        </a>

                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-card">
                            <div class="card" style="width: 600px">
                                <div class="card-body">
                                    @if ($stockMinimum->isNotEmpty())
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h4 class="mb-0">Informasi Stok Habis</h4>
                                            <a href="{{ route('dashboard.out-of-stock') }}" class="btn btn-outline-primary btn-sm rounded-2">
                                                Detail
                                                <i class="fa fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>

                                        <table class="table table-striped" id="stock-minimum">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class="text-center" width="5%">#</th>
                                                    <th scope="col" class="text-center" width="50%">Nama Produk</th>
                                                    <th scope="col" class="text-center" width="10%">Stok Minimal</th>
                                                    <th scope="col" class="text-center" width="10%">Sisa Stok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($stockMinimum as $reminder_stock)
                                                    @if ($reminder_stock->stock_current <= $reminder_stock->minimum_stock)
                                                        @php
                                                            $stock_current = number_format(
                                                                $reminder_stock->stock_current ?? 0,
                                                                2,
                                                                '.',
                                                                '',
                                                            );
                                                        @endphp
                                                        <tr>
                                                            <th class="text-center">{{ $loop->iteration }}</th>
                                                            <td class="text-start">{{ $reminder_stock->name }}</td>
                                                            <td class="text-end">{{ $reminder_stock->minimum_stock }} {{ $reminder_stock->unit_name }}</td>
                                                            <td class="text-end">{{ $stock_current }}  {{ $reminder_stock->unit_name }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach

                                            </tbody>
                                        </table>
                                    @endif

                                    @if ($dueDateInvoices->isNotEmpty())
                                        <h4>Transaksi Mendekati Jatuh Tempo</h4>
                                        <ul>
                                            @foreach ($dueDateInvoices as $invoice)
                                                @php
                                                    // Format pesan yang akan dikirim
                                                    $normalizedPhone = normalizePhoneNumber($invoice->customer_phone);
                                                    $message =
                                                        "Halo {$invoice->customer_name},\n\n" .
                                                        "Ini adalah pengingat untuk transaksi *{$invoice->sale_code}* yang jatuh tempo pada *" .
                                                        dateStandar($invoice->due_date) .
                                                        "*.\n" .
                                                        'Total tagihan yang belum dibayar: *' .
                                                        rupiah($invoice->final_amount - $invoice->total_payment) .
                                                        "*.\n\n" .
                                                        "Silakan lakukan pembayaran sebelum jatuh tempo. Terima kasih. \n\n" .
                                                        "Hormat kami \n\n" .
                                                        $profileCompany->name;

                                                    // Encode pesan untuk URL
                                                    $encodedMessage = urlencode($message);
                                                @endphp
                                                <a
                                                    href="https://web.whatsapp.com/send/?phone={{ $normalizedPhone }}&text={{ $encodedMessage }}">
                                                    <li>
                                                        Transaksi {{ $invoice->sale_code }} untuk
                                                        {{ $invoice->customer_name }} (Jatuh Tempo:
                                                        {{ dateStandar($invoice->due_date) }})
                                                        <br>
                                                        Total Tagihan:
                                                        {{ rupiah($invoice->final_amount - $invoice->total_payment) }}
                                                    </li>
                                                </a>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if ($stockMinimum->isEmpty() && $dueDateInvoices->isEmpty())
                                        <span>Belum ada pemberitahuan</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                        aria-label="Open user menu">
                        <span class="avatar avatar-sm">{{ auth()->user()->users_name[0] }}</span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ auth()->user()->users_name }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-large">
                            Profil
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('logout') }}" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-large" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form action="/profile" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ Auth()->user()->id }}">
                                    @if (Auth()->user()->users_image)
                                        <img class="rounded" id="profile_image_edit" src=""
                                            onerror="this.onerror=null; this.onload=null; if (!this.attributes.src.value) this.attributes.src.value='{{ asset('storage/' . Auth()->user()->users_image) }}'"
                                            width="200px" height="200px" />
                                        <div class="mt-3">
                                            <label for="users_image" class="form-label">Gambar</label>
                                            <input type="file"
                                                class="form-control @error('users_image') is-invalid @enderror"
                                                id="users_image" name="users_image"
                                                onchange="preview_profile_image_edit()">
                                            <small style="color: grey">max: 2MB, type: jpg,jpeg,png</small>
                                            @error('users_image')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <img class="rounded" id="profile_image_empty" src=""
                                            onerror="this.onerror=null; this.src='{{ asset('img/default_img.jpg') }}';"
                                            width="200px" height="200px" />
                                        <div class="mt-3">
                                            <label for="users_image" class="form-label">Gambar</label>
                                            <input type="file"
                                                class="form-control @error('users_image') is-invalid @enderror"
                                                id="users_image" name="users_image"
                                                onchange="preview_profile_image_empty()">
                                            <small style="color: grey">max: 2MB, type: jpg,jpeg,png</small>
                                            @error('users_image')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class=" form-label">Username</label>
                                    <input type="text" autocomplete="off" class="form-control" name="username"
                                        value="{{ Auth()->user()->username }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" autocomplete="off" class="form-control" name="email"
                                        value="{{ Auth()->user()->email }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mb-3">Update</button>
                    </form>

                    <div class="dropdown-divider"></div>
                    <form action="/profile_password" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ Auth()->user()->id }}">
                                    <label class="form-label">Passward Lama</label>
                                    <input type="password" class="form-control" name="password_old">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning mb-3">Update</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-ml" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function preview_profile_image_edit() {
            profile_image_edit.src = URL.createObjectURL(event.target.files[0]);
        }

        function preview_profile_image_empty() {
            profile_image_empty.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <script>
        $(document).ready(function () {
            $('.dropdown-menu').on('click', function (e) {
                e.stopPropagation();
            });

            const stockTable = $('#stock-minimum');
            if (stockTable.length) {
                if ($.fn.DataTable.isDataTable(stockTable)) {
                    stockTable.DataTable().destroy();
                }

                stockTable.DataTable({
                    paging: true,
                    pageLength: 5,
                    searching: false,
                    info: false,
                    lengthChange: false,
                });
            }
        });
    </script>




</header>
