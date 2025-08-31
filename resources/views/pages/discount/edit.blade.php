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
                        <form action="{{ route('discount.update',$data->id) }}" method="post" class="form-detect-unsaved">
                            @csrf
                         @method('PUT')
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Discount Name</label>
                                    <input name="discount_name" type="text" autocomplete="off"
                                        class="form-control @error('discount_name') is-invalid @enderror" value="{{ $data->name }}">
                                    @error('discount_name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Discount Type</label>
                                   <select name="discount_type" class="form-control @error('discount_name') is-invalid @enderror">
                                 @if ($data->type == 'percentage')
                                    <option value="percentage" selected>Percentage</option>
                                    <option value="nominal">Nominal</option>
                                    @else
                                    <option value="nominal" selected>Nominal</option>
                                    <option value="percentage">Percentage</option>
                                    @endif
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
                                        class="form-control @error('value') is-invalid @enderror" value="{{ $data->value }}">
                                    @error('value')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                   @if ($data->status == 'active')
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Tidak Active</option>
                                    @else
                                    <option value="inactive" selected>Tidak Active</option>
                                    <option value="active">Active</option>
                                    @endif
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
