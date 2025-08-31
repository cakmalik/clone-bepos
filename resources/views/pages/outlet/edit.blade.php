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

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Outlet</h3>
                    </div>
                    <form action="{{ route('outlet.update', $outlet->id) }}" method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="row">
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Kode Outlet</label>
                                                <input class="form-control" value="{{ $outlet->code }}" name="code"
                                                    type="text" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Outlet</label>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                                    value="{{ $outlet->name }}" placeholder="Outlet A">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Nomor Telp</label>
                                                <input type="text"
                                                    class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                    value="{{ $outlet->phone }}" placeholder="081234567xxxx">
                                                @error('phone')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Alamat</label>
                                                <textarea type="text" placeholder="Jl. Raya No. 1 Bogor Indonesia 17100" class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ $outlet->address }}</textarea>

                                                @error('address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <img id="imagePreview"
                                                src="{{ asset('storage/images/' . $outlet->outlet_image) }}" alt=""
                                                class="w-25 mb-3">
                                            <div class="mb-3">
                                                <label class="form-label">Gambar</label>
                                                <input type="file"
                                                    class="form-control @error('image') is-invalid @enderror" name="image"
                                                    accept="image/*"
                                                    value="{{ $outlet->outlet_image }}" id="imageInput">
                                                @error('image')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea type="text" placeholder="Menjual berbagai macam produk untuk kebutuhan rumah tangga" class="form-control @error('desc') is-invalid @enderror" name="desc" rows="3">{{ $outlet->desc }}</textarea>
                                                @error('desc')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xl-12">
                                            {{-- Catatan kaki struk --}}
                                            <div class="mb-3">
                                                <label class="form-label">Catatan Kaki Struk</label>
                                                <input type="text"
                                                    class="form-control @error('footer_notes') is-invalid @enderror"
                                                    name="footer_notes" placeholder="Terima Kasih Sudah Berbelanja :)"
                                                    value="{{ $outlet->footer_notes }}">

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
                                <button type="submit" class="btn btn-success ms-auto"><i
                                        class="fa-solid fa-floppy-disk"></i> &nbsp; Update</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
