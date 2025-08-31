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
                       Diskon
                    </h2>
                </div>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <a href="/discount" class="btn btn-primary"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali</a>
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
                        <form action="{{ route('discount.store') }}" method="post" class="form-detect-unsaved">
                            @csrf
                         
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Nama Diskon</label>
                                    <input name="discount_name" type="text" autocomplete="off"
                                        class="form-control @error('discount_name') is-invalid @enderror">
                                    @error('discount_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                   <select name="discount_type" class="form-control @error('discount_name') is-invalid @enderror">
                                    <option value="percentage">Percentage</option>
                                    <option value="nominal">Nominal</option>
                                   </select>
                                    @error('discount_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Nilai</label>
                                    <input name="value" type="number" autocomplete="off"
                                        class="form-control @error('value') is-invalid @enderror">
                                    @error('value')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="active">Active</option>
                                        <option value="inactive">Tidak Active</option>
                                       </select>
                                    @error('status')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary ms-auto"><i
                                            class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
