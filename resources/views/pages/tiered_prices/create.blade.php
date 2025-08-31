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
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $title }}
                    </h2>
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

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">

                        <form action="{{ route('tiered_prices.store') }}" method="post">
                            @csrf
                            <div id="page">
                                <input type="hidden" name="row[]" id="row-array">
                                <div class="row" id="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">Nama Produk</label>
                                            <select class="form-control" name="product_id" id="product_id" required>
                                                <option value="" disabled selected> &mdash; Pilih Produk &mdash;
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label class="form-label">Minimum Qty</label>
                                            <input type="text" class="form-control" name="min_qty_1" id="min_qty_1"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label class="form-label">Maksimum Qty</label>
                                            <input type="text" class="form-control" name="max_qty_1" id="max_qty_1"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3">
                                        <div class="form-group">
                                            <label class="form-label">Harga</label>
                                            <input type="text" class="form-control" name="price_1" id="price_1"
                                                required>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="row">

                                <button type="button" class="btn btn-block btn-primary mt-3" id="tambah_kolom"><i
                                        class="fa-regular fa-plus"></i> &nbsp;Tambah
                                    Kolom</button>
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

        $(document).ready(function() {
            let kolomCounter = 2;
            let row = [1];
            $('#row-array').val(row);

            Array.prototype.remove = function() {
                var what, a = arguments,
                    L = a.length,
                    ax;
                while (L && this.length) {
                    what = a[--L];
                    while ((ax = this.indexOf(what)) !== -1) {
                        this.splice(ax, 1);
                    }
                }
                return this;
            };
            $('#tambah_kolom').on('click', function() {
                $('#row').append(`
                <div class="col-md-4 mt-3 row_input_${kolomCounter}">
                                        <div class="form-group">
                                            <label class="form-label">Minimum Qty</label>
                                            <input type="text" class="form-control" name="min_qty_${kolomCounter}" id="min_qty_${kolomCounter}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-3 row_input_${kolomCounter}">
                                        <div class="form-group">
                                            <label class="form-label">Maksimum Qty</label>
                                            <input type="text" class="form-control" name="max_qty_${kolomCounter}" id="max_qty_${kolomCounter}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3 row_input_${kolomCounter}">
                                        <div class="form-group">
                                            <label class="form-label">Harga</label>
                                            <input type="text" class="form-control" name="price_${kolomCounter}" id="price_${kolomCounter}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-1 row_input_${kolomCounter}" style="margin-top:4.5%">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger hapus" data-id="${kolomCounter}"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>`);

                $('#price_' + kolomCounter).on('keyup', function() {
                    $(this).val(formatRupiah($(this).val(), 'Rp. '));
                });

                $("#min_qty_" + kolomCounter).on("input", function() {

                    let min = $(this).val();
                    minimun = min.replace(/\D/g, '');
                    $(this).val(minimun);
                });
                $("#max_qty_" + kolomCounter).on("input", function() {

                    let max = $(this).val();
                    maximum = max.replace(/\D/g, '');
                    $(this).val(maximum);
                });



                row.push(kolomCounter);
                $('#row-array').val(row);
                kolomCounter++;

            });


            $('body').on('click', '.hapus', function() {
                let id = $(this).data('id');
                $('.row_input_' + id).remove();
                row.remove(id);
                $('#row-array').val(row);
            });

            $('#price_1').on('keyup', function() {
                $(this).val(formatRupiah($(this).val(), 'Rp. '));
            });

            $("#min_qty_1").on("input", function() {

                let min = $(this).val();
                minimun = min.replace(/\D/g, '');
                $(this).val(minimun);
            });
            $("#max_qty_1").on("input", function() {

                let max = $(this).val();
                maximum = max.replace(/\D/g, '');
                $(this).val(maximum);
            });



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


        });
    </script>
@endpush
