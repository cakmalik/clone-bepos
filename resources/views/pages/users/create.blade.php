@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Pengguna
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
                        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="form-detect-unsaved">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Foto Pengguna*</label>
                                                    <input type="file" class="form-control" name="user_image"
                                                        value="{{ old('user_image') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama*</label>
                                                    <input type="text" name="nama" value="{{ old('nama') }}"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">ID Pengguna*</label>
                                                    <input type="text" name="username"
                                                        value="{{ old('username') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Email*</label>
                                                    <input type="email" name="email" value="{{ old('email') }}"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Kata Sandi*</label>
                                                    <input type="text" name="password"
                                                        value="{{ old('password') }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Pin*</label>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <input id="input-pin" type="text" name="pin"
                                                            value="{{ old('pin') }}" class="form-control"
                                                            minlength="6" maxlength="6"
                                                            placeholder="Tambahkan Pin">
                                                        <button type="button" class="btn btn-primary"
                                                            onclick="generatePin()">
                                                            <i class="fas fa-repeat me-2"></i>
                                                            Generate
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Peran Pengguna*</label>
                                                    <select name="role" class="form-control">
                                                        <option value="0" class="text-muted">-- Pilih --</option>
                                                        @foreach ($roles as $role)
                                                            @if (auth()->user()->role->role_name != 'SUPERADMIN' && $role->role_name == 'SUPERADMIN')
                                                                @continue
                                                            @endif
                                                            <option value="{{ $role->id }}"
                                                                @if (old('role') == $role->id) selected @endif>
                                                                {{ $role->role_name }}
                                                            </option>
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
                                    <button class="btn btn-link" id="backButton">Kembali</button>
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
        const digit = 6;

        function generatePin() {
            let pin = ''
            for (let i = 0; i < digit; i++) {
                pin += String(Math.floor(Math.random() * 10));
            }
            $('#input-pin').val(pin);
        }

        $(document).ready(function() {
            $('#input-pin').on('input', function() {
                const value = $(this).val();
                $(this).val(value.split('').filter(v => Boolean(Number(v))).join(''));
            })
        })

        $('#backButton').on('click', function() {
            window.history.back();
        })
    </script>
@endpush
