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
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        {{ $title }}
                    </h2>
                    <a href="/ProductDiscount/create" class="btn btn-primary ">
                        <i class="fas fa-plus me-2"></i>
                        Produk Diskon
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

                <div class="card py-3">
                    <div class="card-body py-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Nama Produk</label>
                                    <select class="form-control" name="product" id="product_search">
                                        <option value="0" disabled selected> &mdash; Pilih Produk &mdash;
                                        </option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Tipe Diskon</label>
                                    <select class="form-control" name="discount_type" id="discount_type">
                                        <option value="0" disabled selected> &mdash; Pilih Tipe Diskon &mdash;
                                        </option>
                                        @foreach ($discount_type as $d)
                                            <option value="{{ $d }}">{{ Str::ucfirst($d) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                id="table-diskon">
                                <thead>
                                    <tr>
                                        <th>Barcode</th>
                                        <th>Nama Produk</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Sampai Tanggal</th>
                                        <th>Tipe Diskon</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Harga Setelah Diskon</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('#product_search').select2({
            ajax: {
                url: 'search-product', // Your server-side search endpoint
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


        $(function() {
            table = $('#table-diskon').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,
                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.product = $('#product_search').val();
                        d.discount_type = $('#discount_type').val();
                    }
                },

                columns: [{
                        data: 'barcode',
                        name: 'barcode'
                    }, {
                        data: 'product_name',
                        name: 'product_name'
                    }, {
                        data: 'start_date',
                        name: 'start_date',
                        render: function(data, type, row) {
                            return moment(data).format('D MMMM Y HH:mm');
                        }
                    },
                    {
                        data: 'expired_date',
                        name: 'expired_date',
                        render: function(data, type, row) {
                            return moment(data).format('D MMMM Y HH:mm');
                        }
                    },
                    {
                        data: 'discount_type',
                        name: 'discount_type'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'after_discount',
                        name: 'after_discount'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    }
                ]

            });
        });


        function productDiscountDelete(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/ProductDiscount') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                location.reload();
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                location.reload();
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };


        $('#start_date, #product_search, #end_date, #discount_type').change(function() {
            table.draw();
        });
    </script>
@endpush
