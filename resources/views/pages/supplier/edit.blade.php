@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Supplier
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
                <div class="col-12">
                    <div class="card py-3">
                        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST" class="form-detect-unsaved">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label class="form-label">Kode Supplier</label>
                                                    <input name="code" type="text" autocomplete="off" readonly
                                                        value="{{ $supplier->code }}"
                                                        class="form-control @error('code') is-invalid @enderror"
                                                        id="code">
                                                    @error('code')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Supplier </label>
                                                    <input name="name" type="text" autocomplete="off"
                                                        value="{{ $supplier->name }}"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        id="name">
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label class="form-label">Nomor Telp </label>
                                                    <input name="phone" type="number" autocomplete="off"
                                                        value="{{ $supplier->phone }}"
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        id="phone">
                                                    @error('phone')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label class="form-label">Alamat </label>
                                                    <input name="address" type="text" autocomplete="off"
                                                        value="{{ $supplier->address }}"
                                                        class="form-control @error('address') is-invalid @enderror"
                                                        id="address">
                                                    @error('address')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3">
                                                    <label class="form-label">Deskripsi</label>
                                                    <textarea name="desc" type="text" autocomplete="off" rows="3"
                                                        class="form-control @error('desc') is-invalid @enderror" id="desc">{{ $supplier->desc }}</textarea>
                                                    @error('desc')
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
                                            class="fa-solid fa-save"></i> &nbsp; Perbarui</button>
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
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
@endpush
