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
                    Pelanggan
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

            <div class="card py-3">
                <div class="card-body py-3">
                    <form action="{{ route('customer.store') }}" method="post" class="form-detect-unsaved">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kode Pelanggan*</label>
                                    <input name="code" type="text" autocomplete="off" value="{{ old('code') }}"
                                        class="form-control @error('code') is-invalid @enderror">
                                    
                                    <small class="form-text text-muted">
                                        <i>
                                        Masukkan kode unik untuk pelanggan. Jika tidak diisi, sistem akan menghasilkan kode otomatis.
                                        </i>
                                    </small>
                                
                                    @error('code')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori Pelanggan*</label>
                                    <select name="customer_category_id" class="form-control select-option" id="customer_category" required>
                                        <option disabled {{ old('customer_category_id') ? '' : 'selected' }}> &mdash; Pilih Kategori &mdash;</option>
                                        @foreach ($customer_category as $cc)
                                            <option value="{{ $cc->id }}" {{ old('customer_category_id') == $cc->id ? 'selected' : '' }}>
                                                {{ $cc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
            
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Pelanggan*</label>
                                    <input name="name" type="text" autocomplete="off" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Telp*</label>
                                    <input name="phone" type="number" autocomplete="off" value="{{ old('phone') }}"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
            
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Alamat*</label>
                                <textarea name="address" rows="3" autocomplete="off"
                                    class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
            
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <select name="province_code" class="form-control select-option" id="province" >
                                        <option disabled {{ old('province_code') ? '' : 'selected' }}> &mdash; Pilih Provinsi &mdash;</option>
                                        @foreach ($province as $p)
                                            <option value="{{ $p->code }}" {{ old('province_code') == $p->code ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kota/Kabupaten</label>
                                    <select name="city_code" class="form-control select-option" id="city" >
                                        <option disabled {{ old('city_code') ? '' : 'selected' }}> &mdash; Pilih Kota/Kabupaten &mdash;</option>
                                        <!-- Isi dinamis berdasarkan Provinsi -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kecamatan</label>
                                    <select name="district_code" class="form-control select-option" id="district" >
                                        <option disabled {{ old('district_code') ? '' : 'selected' }}> &mdash; Pilih Kecamatan &mdash;</option>
                                        <!-- Isi dinamis berdasarkan Kota/Kabupaten -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Desa/Kelurahan</label>
                                    <select name="village_code" class="form-control select-option" id="village" >
                                        <option disabled {{ old('village_code') ? '' : 'selected' }}> &mdash; Pilih Desa/Kelurahan &mdash;</option>
                                        <!-- Isi dinamis berdasarkan Kecamatan -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dusun">Dusun</label>
                                    <select name="sub_village" id="sub_village" class="form-control">
                                        <option disabled {{ old('sub_village') ? '' : 'selected' }}> &mdash; Pilih Dusun &mdash;</option>
                                        <!-- Isi dinamis berdasarkan Desa/Kelurahan -->
                                    </select>
                                </div>
                            </div>
                        </div>
            
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="submit" class="btn btn-primary ms-auto">
                                    <i class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
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
    $('.select-option').select2();
        $('#sub_village').select2({
            placeholder: 'Pilih atau tambah baru',
            tags: true,
            createTag: function(params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            }
        });

        $('#province').on('change', function() {
            $.ajax({
                type: 'post',
                url: "/customer/getCity",
                dataType: 'json',
                data: {
                    province_code: $('#province').val(),
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    $('#city').empty();
                    $.each(data.response, function(key, value) {
                        $('#city').append("<option value=" + value.code + ">" +
                            value.name + "</option>")
                    });
                }
            })
        })

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
@endpush