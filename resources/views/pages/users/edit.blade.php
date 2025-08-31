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
                        <form action="{{ route('users.update', $users->id) }}" method="POST"
                            enctype="multipart/form-data" class="form-detect-unsaved">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Pengguna*</label>
                                                    <input type="text" name="nama"
                                                        value="{{ old('nama', $users->users_name) }}"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">ID Pengguna *</label>
                                                    <input type="text" name="username"
                                                        value="{{ old('username', $users->username) }}"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Email*</label>
                                                    <input type="email" name="email"
                                                        value="{{ old('email', $users->email) }}"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Pin*</label>
                                                    <div class="d-flex" style="gap: 8px;">
                                                        <input id="input-pin" type="text" name="pin"
                                                            class="form-control" minlength="6" maxlength="6"
                                                            value="{{ old('pin', $users->decryptedPin()) }}"
                                                            onchange="setPinChanged()" placeholder="Tambahkan Pin">
                                                        <input id="input-change-pin" type="hidden"
                                                            name="change_pin">
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
                                                        <option value="0">-- Pilih --</option>
                                                        @foreach ($roles as $role)
                                                            @if (auth()->user()->role->role_name == 'SUPERADMIN')
                                                                <option value="{{ $role->id }}"
                                                                    @if ($role->id == old('role', $users->role_id)) selected @endif>
                                                                    {{ $role->role_name }}
                                                                </option>
                                                            @else
                                                                @if ($role->role_name != 'SUPERADMIN')
                                                                    <option value="{{ $role->id }}"
                                                                        @if ($role->id == old('role', $users->role_id)) selected @endif>
                                                                        {{ $role->role_name }}
                                                                    </option>
                                                                @endif
                                                            @endif
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
                                    <button type="submit" class="btn btn-primary ms-auto"><i
                                            class="fa-solid fa-sync"></i> &nbsp; Perbarui
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
            $('#input-change-pin').val(1);
        }

        function setPinChanged() {
            $('#input-change-pin').val(1);
        }

        $('#backButton').click(function() {
            window.history.back();
        });
    </script>
@endpush
