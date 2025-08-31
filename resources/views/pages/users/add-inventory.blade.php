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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <span class="badge bg-primary ms-2 text-capitalize fs-4">
                                    {{ $users->username }}
                                </span>
                            </h3>
                        </div>
                        <form action="{{ route('users.inventory.update', $users->id) }}" method="POST"
                                enctype="multipart/form-data" class="form-detect-unsaved">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <table class="table no-datatable">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>Tipe</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($inventories as $inventory)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $inventory->code }}</td>
                                                        <td>{{ $inventory->name }}</td>
                                                        <td>{{ $inventory->type }}</td>
                                                        <td>
                                                            <input type="checkbox" name="inventories_id[]"
                                                                    class="form-check-input"
                                                                    value="{{ $inventory->id }}" {{ in_array($inventory->id, $userInventories) ? 'checked' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="reset" class="btn btn-link">Muat Ulang</button>
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
        function deleteInventory() {
            const parent = $(this).parent().parent().parent();
            const id = parent.find('input').val();
            inventories_id.splice(inventories_id.indexOf(val));
        }
    </script>
@endpush
