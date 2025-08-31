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
                    <h2 class="page-title text-uppercase">
                        @if (!$editable)
                            Tambah
                        @else
                            Edit
                        @endif mesin kasir
                    </h2>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="/cashier_machine" class="btn btn-primary"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali</a>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">

                    </div>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                @if (!$editable)
                    <div class="card mb-5">
                        <form action="{{ route('cashier_machine.store') }}" method="POST">
                            @csrf
                            <div class="card-body border-bottom py-3">
                                <div class="form-group mb-3">
                                    <label for="outlet" class="label mb-2">Pilih Outlet</label>
                                    <select name="outlet_id" id="outlet"
                                        class="form-select @error('outlet_id') is-invalid @enderror">
                                        @foreach ($outlets as $i)
                                            <option value="{{ $i->id }}">{{ $i->name }}</option>
                                        @endforeach
                                    </select>

                                    @error('outlet_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="outlet" class="label mb-2">Nama Mesin Kasir</label>
                                    <input type="text" class="form-control" name="name" />
                                </div>
                                <button type="submit" class="btn btn-info">Buat</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="card mb-5">
                        <form action="{{ route('cashier_machine.update', $cashier_machine->id) }}" method="POST">
                            @method('PUT')
                            @csrf
                            <div class="card-body border-bottom py-3">
                                <div class="form-group mb-3">
                                    <label for="outlet" class="label mb-2">Outlet</label>
                                    <select name="outlet_id" id="outlet"
                                        class="form-select @error('outlet_id') is-invalid @enderror">
                                        @foreach ($outlets as $i)
                                            <option @selected($i->id == $cashier_machine->outlet_id) value="{{ $i->id }}">
                                                {{ $i->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('outlet_id')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="outlet" class="label mb-2">Nama Mesin Kasir</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ $cashier_machine->name }}" />
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success">Update</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
@endpush
