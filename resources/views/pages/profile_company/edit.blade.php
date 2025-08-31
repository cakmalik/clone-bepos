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
                        Profil Perusahaan
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
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card p-3">
                        <form action="{{ route('profileCompany.update', $dataPC->id) }}" method="POST"
                            enctype="multipart/form-data" class="form-detect-unsaved">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="row">
                                                    <div class="mb-3">

                                                        <label class="form-label">Logo *</label>
                                                        <input name="image" type="file" autocomplete="off"
                                                            id="image"
                                                            class="form-control @error('image') is-invalid @enderror"
                                                            value="{{ $dataPC->image }}">
                                                        @error('image')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">Nama Perusahaan *</label>
                                                        <input name="name" type="text"
                                                            value="{{ $dataPC->name }}" required autocomplete="off"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            required>
                                                        @error('name')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">About *</label>
                                                        <textarea name="about" type="text" required autocomplete="off" rows="3"
                                                            class="form-control @error('about') is-invalid @enderror" required>{{ $dataPC->about }}</textarea>
                                                        @error('about')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">No Telp *</label>
                                                        <input name="telp" type="text" required
                                                            value="{{ $dataPC->telp }}" autocomplete="off"
                                                            class="form-control @error('telp') is-invalid @enderror"
                                                            required>
                                                        @error('telp')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email *</label>
                                                        <input name="email" type="email"
                                                            value="{{ $dataPC->email }}" required
                                                            autocomplete="off"
                                                            class="form-control @error('email') is-invalid @enderror"
                                                            required>
                                                        @error('email')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">Alamat *</label>
                                                        <textarea name="address" type="text" required autocomplete="off"
                                                            class="form-control @error('address') is-invalid @enderror" required>{{ $dataPC->address }}</textarea>
                                                        @error('address')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- <div class="row">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status *</label>
                                                        <select name="status" id="" class="form-control">
                                                            @if ($dataPC->status == 'active')
                                                                <option value="active">Aktif</option>
                                                                <option value="inactive">Tidak Aktif</option>
                                                            @else
                                                                <option value="inactive">Tidak Aktif</option>
                                                                <option value="active">Aktif</option>
                                                            @endif
                                                        </select>
                                                        @error('telp')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div> --}}

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="col-md-12 mb-2 ml-6">
                                                    <img id="preview-image-before-upload"
                                                        style="max-height: 550px;"
                                                        src="
                                                    @if ($dataPC->image == 'betech.png') {{ asset('logocompany/' . $dataPC->image) }}
                                                        @else
                                                        {{ asset('storage/images/' . $dataPC->image) }} @endif

                                                        ">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary ms-auto"><i
                                                class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan</button>
                                    </div>
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
        $(document).ready(function(e) {


            $('#image').change(function() {

                let reader = new FileReader();

                reader.onload = (e) => {

                    $('#preview-image-before-upload').attr('src', e.target.result);
                }

                reader.readAsDataURL(this.files[0]);

            });

        });
    </script>
@endpush
