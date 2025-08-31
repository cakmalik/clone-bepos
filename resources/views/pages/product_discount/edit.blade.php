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

                <div class="card pt-3">
                    <div class="card-body border-bottom py-3">
                        <form action="{{ route('ProductDiscount.update', $productDiscount->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                            @method('put')
                            <div class="row">
                                <input type="hidden" name="id" value="{{ $productDiscount->id }}">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nama Produk</label>
                                        <input type="hidden" name="product_id" value="{{ $productDiscount->product->id }}">
                                        <input class="form-control  @error('product_id') is-invalid @enderror"
                                            id="product_id" value="{{ $productDiscount->product->name }}" readonly>
                                        @error('product_id')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Tipe Diskon</label>
                                        <select class="form-control  @error('discount_type') is-invalid @enderror"
                                            name="discount_type" id="discount_type">
                                            <option value="0" disabled selected> &mdash; Pilih Tipe Diskon &mdash;
                                            </option>
                                            @foreach ($discount_type as $d)
                                                @if ($productDiscount->discount_type == $d)
                                                    <option value="{{ $d }}" selected>{{ Str::ucfirst($d) }}
                                                    </option>
                                                @else
                                                    <option value="{{ $d }}">{{ Str::ucfirst($d) }}</option>
                                                @endif
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
                                            name="start_date" id="start_date" value="{{ $productDiscount->start_date }}"
                                            required>
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
                                            name="expired_date" id="expired_date"
                                            value="{{ $productDiscount->expired_date }}" required>
                                        @error('expired_date')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Harga Produk</label>
                                        <input type="text" class="form-control" id="price"
                                            value="{{ $productDiscount->product->productPriceUtama->price }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Harga Setelah Diskon</label>
                                        <input type="text" class="form-control" id="final_harga"
                                            value="{{ $final_harga }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-label">Jumlah</label>
                                        <input type="text" class="form-control  @error('amount') is-invalid @enderror"
                                            name="amount" id="amount" value="{{ $amount }}" required>
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
                                            class="fa-solid fa-refresh"></i> &nbsp; Perbarui
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
    <script>
        $(document).ready(function() {


            let type = $('#discount_type').val();
            if (type == 'NOMINAL') {
                $('#info').append("<small class='text-red'> Maksimal Diskon " + formatRupiah($('#price').val(),
                    'Rp.') + "</small>")
            } else {
                $('#info').append("<small class='text-red'> Maksimal Diskon 100% </small>")
            }
        })
        $('#amount').on('keyup', function() {
            let discount_type = $('#discount_type').val();
            if (discount_type == 'NOMINAL') {
                $(this).val(formatRupiah($(this).val(), 'Rp.'));

                let price = parseFloat($('#price').val()); // Ubah ke tipe data number

                let jumlah = $(this).val().replace(/Rp\. /g, '').replace(/\./g, '');


                $('#final_harga').val(parseFloat(price - jumlah));


                if (parseFloat(jumlah) > price) { // Ubah ke tipe data number
                    $(this).val('');
                    $('#final_harga').val('');
                }

            } else {
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
            }
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
