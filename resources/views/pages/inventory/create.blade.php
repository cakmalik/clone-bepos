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
                        Gudang
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
                        <form action="{{ route('inventory.store') }}" method="POST" class="form-detect-unsaved">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" autocomplete="off"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Tipe</label>
                                                <select name="type"
                                                    class="form-control @error('type') is-invalid @enderror">
                                                    <option value="0" disabled selected> &mdash; Pilih Tipe
                                                        &mdash;
                                                    </option>
                                                    @foreach ($types as $type)
                                                        <option value="{{ $type['value'] }}"
                                                            {{ old('type') == $type['value'] ? 'selected' : '' }}>
                                                            {{ $type['text'] }}</option>
                                                    @endforeach
                                                </select>
                                                @error('type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Deskripsi</label>
                                                <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                            </div>
                                            <div class="col-12 d-flex">
                                                <label class="form-label me-3">Gudang Induk</label>
                                                <div>
                                                    <input type="checkbox" name="is_parent"
                                                        class="form-check-input @error('is_parent') is-invalid @enderror"
                                                        value="1" onchange="toggleParentSelection(this)"
                                                        {{ old('is_parent') ? 'checked' : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3" id="select-parent">
                                                <select name="parent_id"
                                                    class="form-control @error('parent_id') is-invalid @enderror">
                                                    <option selected value="0" disabled> &mdash; Pilih Gudang
                                                        Induk
                                                        &mdash;
                                                    </option>
                                                    @foreach ($parents as $parent)
                                                        <option value="{{ $parent['id'] }}"
                                                            {{ old('parent_id') == $parent['id'] ? 'selected' : '' }}>
                                                            {{ $parent['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 mb-3 d-flex">
                                                <label class="form-label me-3">Aktif</label>
                                                <div>
                                                    <input type="checkbox" name="is_active"
                                                        class="form-check-input @error('is_active') is-invalid @enderror"
                                                        value="1" {{ old('is_active') ? 'checked' : '' }}">
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
                                            class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan
                                    </button>
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
        function toggleParentSelection(el) {
            if (el.checked) {
                $('#select-parent').hide();
            } else {
                $('#select-parent').show();
            }
        }
    </script>
@endpush
