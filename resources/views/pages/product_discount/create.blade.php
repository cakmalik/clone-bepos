@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Produk Diskon
                    </h2>
                </div>

                <div class="col-auto">
                    <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm rounded-2">
                        <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali
                    </a>
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

                <div class="card p-3">
                    <div class="card-body py-3">
                        <form action="{{ route('ProductDiscount.store') }}" method="post" class="form-detect-unsaved">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Produk</label>
                                        <select class="form-control  @error('product_id') is-invalid @enderror"
                                            name="product_id" id="product_id">
                                            <option value="0" disabled selected> &mdash; Pilih Produk &mdash;
                                            </option>

                                        </select>
                                        @error('product_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Tipe Diskon</label>
                                        <select class="form-control  @error('discount_type') is-invalid @enderror"
                                            name="discount_type" id="discount_type" disabled>
                                            <option value="0" disabled selected> &mdash; Pilih Tipe Diskon &mdash;
                                            </option>
                                            @foreach ($discount_type as $d)
                                                <option value="{{ $d }}">{{ Str::ucfirst($d) }}</option>
                                            @endforeach
                                        </select>
                                        @error('discount_type')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Mulai Tanggal</label>
                                        <input type="datetime-local"
                                            class="form-control  @error('start_date') is-invalid @enderror"
                                            name="start_date" id="start_date" required>
                                        @error('start_date')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Sampai Tanggal</label>
                                        <input type="datetime-local"
                                            class="form-control  @error('expired_date') is-invalid @enderror"
                                            name="expired_date" id="expired_date" required>
                                        @error('expired_date')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Harga Produk</label>
                                        <input type="text" class="form-control" id="price" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Harga Setelah Diskon</label>
                                        <input type="text" class="form-control" id="final_harga" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Jumlah</label>
                                        <input type="text" class="form-control  @error('amount') is-invalid @enderror"
                                            name="amount" id="amount" required>
                                        <div id="info"> </div>
                                        @error('amount')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end mt-3">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ms-auto"><i
                                            class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#product_id').select2({
            ajax: {
                url: '/search-product', // Your server-side search endpoint
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // Search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Minimum characters before a search is performed
        });

        $('#expired_date').on('change', function() {
            // Ambil nilai dari input tanggal mulai

            const startDate = $('#start_date').val();
            // Cek apakah nilai input tanggal mulai telah diisi
            if (startDate) {
                // Ambil nilai dari input tanggal berakhir
                const expiredDateInput = $('#expired_date');
                const expiredDateValue = expiredDateInput.val();

                // Buat objek Date dari tanggal berakhir
                const endDate = new Date(expiredDateValue);

                // Jika nilai input tanggal berakhir kosong atau kurang dari tanggal mulai, set tanggal berakhir sama dengan tanggal mulai
                if (!expiredDateValue || endDate < new Date(startDate)) {
                    // Buat objek Date dari tanggal mulai
                    const startDateObj = new Date(startDate);

                    // Tambahkan 1 menit ke tanggal mulai
                    startDateObj.setMinutes(startDateObj.getMinutes() + 1);

                    const formattedDate = startDateObj.toLocaleString('en-GB', {
                        timeZone: 'Asia/Jakarta'
                    });
                    expiredDateInput.val(formattedDate);
                }
            }
        });



        $('#product_id').on('change', function() {
            $('#discount_type').removeAttr('disabled');
            $('#final_harga').val('');
            $('#amount').val('');
            $.ajax({
                url: "/getPriceProduct",
                type: "POST",
                data: {
                    product_id: $(this).val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#price').val('');
                    $('#price').val(response.price);
                    $('#info').empty();
                    $('#info').append("<small class='text-red'> Maksimal Diskon " + formatRupiah($(
                            '#price').val(),
                        'Rp.') + "</small>");
                }
            })


        });



        $('#discount_type').on('change', function() {
            $('#amount').removeAttr('readonly');
            $('#amount').val('');
            $('#final_harga').val('');
            $('#amount').off('keyup');
            if ($(this).val() == 'NOMINAL') {


                amount.addEventListener('keyup', function(e) {
                    amount.value = formatRupiah(this.value, 'Rp.');
                });

                $('#amount').on('keyup', function() {
                    let price = parseFloat($('#price').val()); // Ubah ke tipe data number

                    let jumlah = $(this).val().replace(/Rp\. /g, '').replace(/\./g, '');
                    $('#final_harga').val(parseFloat(price - jumlah));
                    if (parseFloat(jumlah) > price) { // Ubah ke tipe data number
                        $(this).val('');
                        $('#final_harga').val('');
                    }
                });

                $('#info').empty();
                $('#info').append("<small class='text-red'> Maksimal Diskon " + formatRupiah($('#price').val(),
                    'Rp.') + "</small>")

            } else {
                $('#info').empty();
                $('#info').append("<small class='text-red'> Maksimal Diskon 100% </small>")
                $('#amount').on('keyup', function() {
                    $(this).val($(this).val().replace(/[^,\d]/g, ''));
                    let price = parseFloat($('#price').val());

                    let jumlah = $(this).val().replace(/Rp\. /g, '').replace(/\./g, '');
                    let percentage = parseInt(jumlah);

                    // Periksa apakah nilai percentage berada di antara 0 hingga 100
                    if (percentage >= 0 && percentage <= 100) {
                        $('#final_harga').val(parseFloat(price - (price * (percentage / 100))));
                    } else {
                        // Jika percentage di luar rentang 0-100, set harga akhir ke 0
                        $('#final_harga').val(0);
                    }

                    let persen = $(this).val();

                    if (persen > 100) {
                        $(this).val('')
                    }

                })

            }
        })






        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
