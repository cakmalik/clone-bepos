@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Menu
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
                                    <h3 class="card-title">Tambah Menu</h3>
                                </div>
                                <form action="{{ route('menu.store') }}" method="POST">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <div class="row">
                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Menu *</label>
                                                            <input type="text" class="form-control" name="nama_menu"
                                                                value="{{ old('name') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">is parent ? *</label>
                                                            <input type="checkbox" name="is_parent"  id="is_parentt"  onclick="enableParent()">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-xl-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Parent *</label>
                                                           <select name="parent" class="form-control" id="parentt">
                                                                <option value="0">Pilih Parent</option>
                                                                @foreach ($menus as $menu)
                                                                <option value="{{ $menu->id }}">{{ $menu->menu_name }}</option>
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
@include('sweetalert::alert')
@endsection
@push('scripts')
<script>
    function enableParent() {
    if (document.getElementById("is_parentt").checked) {
      document.getElementById("parentt").disabled = true;

    } else {
      document.getElementById("parentt").disabled = false;

    }
  }
</script>
@endpush
