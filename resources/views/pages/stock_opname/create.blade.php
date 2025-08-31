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
                        <h3 class="card-title"> {{ $title }} </h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <form action="{{ route('stockOpname.create') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Tipe</label>
                                        <select class="form-control" name="type" id="type">
                                            <option value="GUDANG" selected>GUDANG</option>
                                            <option value="OUTLET">OUTLET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Gudang</label>
                                        <select class="form-control" name="inventory_id" id="inventory_id">
                                            <option value="" disabled selected> &mdash; Pilih Gudang &mdash;
                                            </option>
                                            @foreach ($inventory as $inv)
                                                <option value="{{ $inv->id }}">{{ $inv->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="type">Outlet</label>
                                        <select class="form-control" name="outlet_id" id="outlet_id">
                                            <option value="" disabled selected> &mdash; Pilih Outlet &mdash;
                                            </option>
                                            @foreach ($outlet as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <hr>

                            <div class="row">
                                <div class="btn-list">
                                    <button type="button" id="showItem" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg>
                                        Produk
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mt-4">
                                    <label class="form-label">Nama Produk</label>
                                </div>
                                <div class="col-md-3 mt-4">
                                    <label class="form-label">Qty</label>
                                </div>

                            </div>
                            <div class="row" id="page-opname">

                            </div>


                            <div class="card-footer text-end mt-3">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ms-auto" id="btnOP"><i
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
@include('pages.stock_opname.items')
@endsection
@push('scripts')
    <script>
        $(function() {

            $(document).on("keyup", '#dataTable_filter input', function() {
                table.draw();
            });

            let items = [];

            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                order: false,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.product = $('#dataTable_filter input').val();
                        d.selected_product = items;

                    }
                },

                columns: [{
                        data: 'barcode',
                        name: 'barcode'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data, type, row) {
                            var words = data.split(' ').slice(0, 5).join(' ');
                            var ellipsis = data.split(' ').length > 5 ? '...' : '';
                            return '<span class="truncated-text" title="' + data + '">' + words +
                                ellipsis + '</span>';
                        }
                    },
                    {
                        data: 'product_category',
                        name: 'product_category'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        "className": "dt-center",
                        data: 'action',
                        name: 'action'
                    }

                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    var data = api.ajax.json();
                    console.log('Received Data:', data);
                }
            });


            $('#showItem').click(function() {
                table.draw();
                $('#itemOpname').modal('show');
            });


            $('body').on('click', '#items_opname', function() {
                let id = $(this).data('id');

                console.log('Dipilih ='+id);
                items.push(id);
                $(this).hide()
                $(this).replaceWith(
                    "<span class='badge badge-sm bg-green'>Dipilih</span>");

                $.ajax({
                    url: "{{ url('/getProductOpname') }}",
                    type: "POST",
                    data: {
                        items: [id],
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        $.each(response.response, function(key, value) {
                            $('#page-opname').append(
                                "<input type='hidden'  class='stock_opname_" +
                                value.id + "' name='items[]' value='" +
                                value.id + "'>");
                            $('#page-opname').append(
                                "<div class='col-md-8 mt-3 stock_opname_" +
                                value.id +
                                "'><input type='text' class='form-control' name='" +
                                value.id + "_name' value='" + value
                                .name + "' readonly ></div>");

                            $('#page-opname').append(
                                "<div class='col-md-3 mt-3 stock_opname_" +
                                value.id +
                                "'><input type='text'  class='form-control' name='" +
                                value.id + "_qty' id='" +
                                value.id + "_qty' value='0' required></div>");

                            $('#page-opname').append(
                                "<div class='col-md-1 mt-3 stock_opname_" +
                                value.id +
                                "'><button type='button' id='removeProduct' class='btn btn-secondary btn-sm py-2 px-3' data-id='" +
                                value.id +
                                "'><li class='fas fa-trash'></li></button></div>"
                            );

                            
                            $("#" + value.id + "_qty").on("input", function() {

                                let qty = $(this).val();
                                input = qty.replace(/\D/g, '');
                                $(this).val(input);
                            });
                        });

                       


                    }

                });
                $('#btnOP').show();
            });

            $('body').on('click', '#removeProduct', function() {
                let id = $(this).data('id');
                $('.stock_opname_' + id).remove()
                items.remove(id);

                let elementId = $('#opname_product_'+id);

                console.log('Dihapus ='+id);



                let elemenTd = $('[data-id="opname_product_' +
                    id +
                    '"]').closest('td');

                if (elemenTd.length > 0) {
                    // Buat elemen baru untuk menggantikan yang ada
                    let linkElement =
                        '<a href="javascript:void(0)" id="items_opname" class="btn btn-primary btn-sm py-2 px-3" data-id="' +
                        id +
                        '"><li class="fas fa-add"></li></a>';

                    // Ganti elemen yang ada dengan elemen baru
                    elemenTd.html(linkElement);
                }
                if (items.length === 0) {
                    $('#btnOP').hide();

                }

            });
        });
    </script>
    <script>
        $(document).ready(function() {


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
            $('#btnOP').hide();


           

            let type = $('#type').val();

            if (type === 'GUDANG') {
                $('#inventory_id').prop('disabled', false);
                $('#inventory_id').prop('required', true);
                $('#outlet_id').prop('required', false);
                $('#outlet_id').prop('disabled', true);
                $('#outlet_id').prop('selectedIndex', 0);
            } else {
                $('#inventory_id').prop('disabled', true);
                $('#inventory_id').prop('selectedIndex', 0);
                $('#outlet_id').prop('disabled', false);
                $('#outlet_id').prop('required', true);
                $('#inventory_id').prop('required', false);
            }
            $('#type').on('change', function() {

                if ($(this).val() === 'GUDANG') {
                    $('#inventory_id').prop('disabled', false);
                    $('#inventory_id').prop('required', true);
                    $('#outlet_id').prop('required', false);
                    $('#outlet_id').prop('disabled', true);
                    $('#outlet_id').prop('selectedIndex', 0);
                } else {
                    $('#inventory_id').prop('disabled', true);
                    $('#inventory_id').prop('selectedIndex', 0);
                    $('#outlet_id').prop('disabled', false);
                    $('#outlet_id').prop('required', true);
                    $('#inventory_id').prop('required', false);
                }
            })
        });
    </script>
@endpush
