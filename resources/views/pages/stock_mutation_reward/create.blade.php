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
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-md-4">
                                <span>Nama Outlet </span>
                                <h3>{{ getOutletActive()->name }}</h3>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Tipe Mutasi</label>
                                    <select class="form-control @error('type') is-invalid @enderror" name="type"
                                        id="type" required>
                                        <option value="0" disabled selected> &mdash; Pilih Tipe Mutasi &mdash;
                                        </option>

                                        @foreach ($type as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach

                                    </select>
                                    @error('type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        @include('pages.stock_mutation_reward.product')

                        <form action="/stock_mutation_reward" method="post">
                            @csrf
                            <input type="hidden" name="type" id="type_mutation">
                            <div class="row">
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Stok Produk</label>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>
                            </div>
                            <div class="row" id="page_product_list">

                            </div>

                            <div class="card-footer text-end mt-3">
                                <div class="d-flex">
                                    <button type="submit" id="btnReward" class="btn btn-primary ms-auto"><i
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
    <script>
        $(document).ready(function() {

            $('#btnReward').hide();
            let items = [];

            $('#type').on('change', function() {
                $('#type_mutation').val($(this).val());
                $('#page_product_list').empty();
                items = [];
                $('#btnReward').hide();
                $.ajax({
                    url: "{{ url('/get_product_reward') }}",
                    type: "POST",
                    data: {
                        type: $(this).val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $('#tbody-product-reward').empty();
                        $.each(response, function(key, value) {
                            $('#tbody-product-reward').append('<tr><td>' + value.name +
                                '</td><td>' + value.stok +
                                '</td><td id="product_list" data-id="product_list_' +
                                value.id +
                                '"><a href="javascript:void(0)" id="product_reward" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                                value.id +
                                '"><li class="fas fa-add"></li></a></td></tr>')
                        });

                    }

                });

            });


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


            $('body').on('click', '#product_reward', function() {
                let id = $(this).data('id');
                items.push(id);
                $(this).hide()
                $(this).replaceWith(
                    "<span class='badge badge-sm bg-green'>Dipilih</span>");

                $.ajax({
                    url: "{{ url('/getProduct_reward') }}",
                    type: "POST",
                    data: {
                        items: [id],
                        type: $('#type').val(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $.each(response, function(key, value) {
                            $('#page_product_list').append(
                                "<input type='hidden'  class='stock_mutation_reward_" +
                                value.id + "' name='product_reward[]' value='" +
                                value.id + "'>");


                            $('#page_product_list').append(
                                "<div class='col-md-4 mt-3 stock_mutation_reward_" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id + "_name' value='" + value
                                .name + "' readonly ></div>");

                            $('#page_product_list').append(
                                "<div class='col-md-4 mt-3 stock_mutation_reward_" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id + "_qty_current' value='" + value
                                .stok +
                                "' id='" + value.id +
                                "_qty_current' readonly></div>");

                            $('#page_product_list').append(
                                "<div class='col-md-3 mt-3 stock_mutation_reward_" +
                                value.id +
                                "'><input type='text' min='0' class='form-control' name='" +
                                value.id + "_qty' id='" + value.id +
                                "_qty' value='0'></div>");


                            $('#page_product_list').append(
                                "<div class='col-md-1 mt-3 stock_mutation_reward_" +
                                value.id +
                                "'><button type='button' id='removeProduct' class='btn btn-secondary btn-sm py-2 px-3' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></button></div>"
                            );


                            $('body').on('click', '#removeProduct', function() {
                                let id = $(this).data('id');
                                $('.stock_mutation_reward_' + id).remove()
                                items.remove(id);
                                let elemenTd = $('[data-id="product_list_' +
                                    id +
                                    '"]').closest('td');

                                if (elemenTd.length > 0) {
                                    // Buat elemen baru untuk menggantikan yang ada
                                    let linkElement =
                                        '<a href="javascript:void(0)" id="product_reward" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                                        id +
                                        '"><li class="fas fa-add"></li></a>';

                                    // Ganti elemen yang ada dengan elemen baru
                                    elemenTd.html(linkElement);

                                }



                                if (items.length === 0) {
                                    $('#btnReward').hide();

                                }

                            });


                            $("#" + value.id + "_qty").on("input", function() {
                                let qty = $(this).val();
                                qty_input = qty.replace(/\D/g, '');
                                $(this).val(qty_input);


                            });


                            $("#" + value.id + "_qty").on("keyup", function() {
                                let qty = $(this).val();
                                let stok_current = $('#' + value.id +
                                    "_qty_current").val();

                                if (parseInt(qty) > parseInt(stok_current)) {
                                    $(this).val(0)
                                }

                            });
                        });

                    }

                });
                $('#btnReward').show();
            });

        });
    </script>
@endpush
