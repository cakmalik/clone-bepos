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
                        Pelanggan
                    </h2>
                    <div class="btn-list">
                        <a href="/customer/create" class="btn btn-primary ">
                            <i class="fa-solid fa-plus"></i>&nbsp;
                            Pelanggan
                        </a>
                        <button class="btn btn-danger" type="button" id="exportPdf">
                            <i class="fa-solid fa-file-pdf"></i>&nbsp;
                            PDF
                        </button>
                        <button class="btn btn-success" type="button" id="exportExcel">
                            <i class="fa-solid fa-file-excel"></i>&nbsp;
                            Excel
                        </button>
                    </div>
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
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kota/Kabupaten</label>
                                    <select name="city_code" class="form-select" id="city">
                                        <option selected value=""> &mdash; Semua Kota &mdash;
                                        </option>
                                        @foreach ($cities as $value)
                                            <option value="{{ $value->code }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Kecamatan</label>
                                    <select name="district_code" class="form-select" id="district">
                                        <option selected value=""> &mdash; Semua Kecamatan &mdash;
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Desa/Kelurahan</label>
                                    <select name="village_code" class="form-select" id="village">
                                        <option selected value=""> &mdash; Semua Desa &mdash;
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Dusun</label>
                                    <select name="sub_village" id="sub_village" class="form-select">
                                        <option selected value="0" disabled> &mdash; Pilih Dusun &mdash;
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="dataTable"
                                class="table card-table table-vcenter text-nowrap
                            datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 8%">No</th>
                                        <th>Kode</th>
                                        <th>Kategori/Member</th>
                                        <th>Nama</th>
                                        <th>Telp</th>
                                        <th>Kota</th>
                                        <th>Kecamatan</th>
                                        <th>Desa</th>
                                        <th>Dusun</th>
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
        $(function() {

            $('.form-select').select2();

            let appUrl = {!! json_encode(url('/')) !!};
            let city = $('#city').val();
            let district = $('#district').val();
            let village = $('#village').val();
            let sub_village = $('#sub_village').val();

            $(document).on("keyup", '#dataTable_filter input', function() {
                table.draw();
            });

            let table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                destroy: true,

                ajax: {
                    url: '{{ url()->current() }}',
                    data: function(d) {
                        d.city = city;
                        d.district = district;
                        d.village = village;
                        d.sub_village = sub_village;
                    }
                },

                columns: [{
                        className: 'dt-center',
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'city_name',
                        name: 'city_name'
                    },
                    {
                        data: 'district_name',
                        name: 'district_name'
                    },
                    {
                        data: 'village_name',
                        name: 'village_name'
                    },
                    {
                        data: 'sub_village',
                        name: 'sub_village'
                    },
                    {
                        "className": "dt-center",
                        data: 'action',
                        name: 'action'
                    }

                ]
            });

            $('#city, #district, #village, #sub_village').on('change', function() {
                city = $('#city').val();

                if (city == '') {
                    $('#district').empty();
                    $('#village').empty();
                    $('#sub_village').empty();

                }

                district = $('#district').val();
                village = $('#village').val();
                sub_village = $('#sub_village').val();


                table.draw();

            });


            $('#exportPdf').on('click', function() {
                let urlExport = appUrl + '/customer-export?type=pdf&city=' + city + '&district=' +
                    district + '&village=' + village +
                    '&sub_village=' + sub_village;

                window.open(urlExport, "_blank");
            });

            $('#exportExcel').on('click', function() {
                let urlExport = appUrl + '/customer-export?type=excel&city=' + city + '&district=' +
                    district + '&village=' + village +
                    '&sub_village=' + sub_village;

                window.open(urlExport, "_blank");
            });

        });


        $('#city').on('change', function() {

            $.ajax({
                type: 'post',
                url: "/customer/getDistrict",
                dataType: 'json',
                data: {
                    city_code: $('#city').val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#district').empty();
                    $('#district').append("<option value=''>-- Semua Kecamatan --</option>");
                    $.each(data.response, function(key, value) {
                        $('#district').append("<option value=" + value.code +
                            ">" + value.name + "</option>")
                    });
                }
            })
        })

        $('#district').on('change', function() {
            $.ajax({
                type: 'post',
                url: "/customer/getVillage",
                dataType: 'json',
                data: {
                    district_code: $('#district').val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#village').empty();
                    $('#village').append("<option value=''>-- Semua Desa --</option>");
                    $.each(data.response, function(key, value) {
                        $('#village').append("<option value=" + value.code + ">" +
                            value.name + "</option>")
                    });
                }
            })
        })


        $('#village').on('change', function() {
            $.ajax({
                type: 'post',
                url: "/customer/getSubvillage",
                dataType: 'json',
                data: {
                    village_code: $('#village').val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#sub_village').empty();
                    $.each(data.response, function(key, value) {
                        $('#sub_village').append("<option value=" + value.name + ">" +
                            value.name + "</option>")
                    });
                }
            })
        })
    </script>
    <script>
        function customerDelete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/customer') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                    window.location.href = '/customer';
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                window.location.href = '/customer';
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi di hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
