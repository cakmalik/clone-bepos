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
                        Tambah Profil Perusahaan
                    </h2>
                </div>
            </div>
        </div>
    </div>



    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        {{-- @if ($message = Session::get('error'))
                        <x-alert level="danger" message="{{ $message }}" />
                    @elseif($message = Session::get('success'))
                        <x-alert level="success" message="{{ $message }}" />
                    @endif
                        @foreach ($errors->all() as $error)
                            <x-alert level="danger" message="{{ $error }}" />
                        @endforeach --}}
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tambah Profil Perusahaan</h3>
                                </div>
                                <form action="{{ route('profileCompany.store') }}" method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-6">
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="row">
                                                            <div class="mb-3">

                                                                <label class="form-label">Logo *</label>
                                                                <input name="image" type="file" required autocomplete="off" id="image" class="form-control @error('image') is-invalid @enderror" required>
                                                                @error('image')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">Nama Perusahaan *</label>
                                                                <input name="name" type="text" required autocomplete="off" class="form-control @error('name') is-invalid @enderror" required>
                                                                @error('name')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">About *</label>
                                                                <textarea name="about" type="text" required autocomplete="off" rows="3" class="form-control @error('about') is-invalid @enderror" required></textarea>
                                                                @error('about')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">No Telp *</label>
                                                                <input name="telp" type="number" required autocomplete="off" class="form-control @error('telp') is-invalid @enderror" required>
                                                                @error('telp')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">Email *</label>
                                                                <input name="email" type="email" required autocomplete="off" class="form-control @error('email') is-invalid @enderror" required>
                                                                @error('email')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">Alamat *</label>
                                                                <textarea name="address" type="text" required autocomplete="off" class="form-control @error('address') is-invalid @enderror" required></textarea>
                                                                @error('address')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        {{-- <div class="row">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status *</label>
                                                                <select name="status" id=""></select>
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
                                                                    style="max-height: 550px;">
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <div class="d-flex">
                                            <button type="reset" class="btn btn-link">Reset</button>
                                            <button type="submit" class="btn btn-primary ms-auto"><i class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@push('scripts')
<script>
    $(document).ready(function (e) {


$('#image').change(function(){

let reader = new FileReader();

reader.onload = (e) => {

$('#preview-image-before-upload').attr('src', e.target.result);
}

reader.readAsDataURL(this.files[0]);

});

});
</script>
@endpush
