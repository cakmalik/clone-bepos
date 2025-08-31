@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Setting Outlet
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
                                    <h3 class="card-title">Outlet Active saat ini : {{ $cariOutletActive->name }}</h3>
                                </div>
                                <form action="{{ route('outlet.setting.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Piih Outlet Active *</label>
                                                           <select name="outlet_id" class="form-control">
                                                                <option value="0">Pilih Outlet</option>
                                                                @foreach ($outlets as $outlet)
                                                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                                                @endforeach
                                                           </select>
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
</script>
@endpush
