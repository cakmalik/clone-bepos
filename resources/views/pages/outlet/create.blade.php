@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Outlet
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
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}
                <div class="card py-3">
                    <form action="{{ route('outlet.store') }}" method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Outlet</label>
                                                <input class="form-control" value="{{ $autoCode }}"
                                                    name="code" type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Outlet</label>
                                                <input type="text" placeholder="Outlet A"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Nomor Telp</label>
                                                <input type="text" placeholder="081234567xxxx"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    name="phone">
                                                @error('phone')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Alamat</label>
                                                <textarea type="text" class="form-control @error('address') is-invalid @enderror" name="address" rows="3" placeholder="Jl. Raya No. 1 Bogor Indonesia 17100"></textarea>

                                                @error('address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Gambar</label>
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror"
                                                    name="image">
                                                @error('image')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea type="text" class="form-control @error('desc') is-invalid @enderror" name="desc" rows="3" placeholder="Menjual berbagai macam produk untuk kebutuhan rumah tangga"></textarea>
                                                @error('desc')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Catatan Kaki Struk</label>
                                                <input type="text"
                                                    class="form-control @error('footer_notes') is-invalid @enderror"
                                                    name="footer_notes" placeholder="Terima Kasih Sudah Berbelanja :)">

                                                @error('footer_notes')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <button type="reset" class="btn btn-link">Reset</button>
                                <button type="submit" class="btn btn-primary ms-auto"><i
                                        class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script></script>
@endpush
