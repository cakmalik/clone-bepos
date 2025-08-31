@extends('layouts.app')
@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Hak Akses
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
                        <form action="{{ route('permission.store') }}" method="POST" class="form-detect-unsaved">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="row">
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Peran Pengguna*</label>
                                                    <select name="role_id" class="form-control">
                                                        <option value="0" class="text-muted" disabled>-- Pilih --</option>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}">
                                                                {{ $role->role_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xl-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Hak Akses Pada Menu*</label>
                                                    <div class="table-responsive">
                                                        <table
                                                            class="myCustomClass table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Menu</th>
                                                                    <th>Permission</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($menus as $menu)
                                                                    <tr>
                                                                        <td>{{ $menu->menu_name }}</td>
                                                                        <td
                                                                            style="text-align: center; vertical-align: middle;">
                                                                            <input type="checkbox"
                                                                                name="menu_permission[]"
                                                                                value="{{ $menu->id }}"
                                                                                style="transform: scale(1.5);">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <div class="d-flex">
                                    <button type="reset" class="btn btn-link">Muat Ulang</button>
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
@push('scripts')
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('.myCustomClass')) {
                $('.myCustomClass').DataTable().destroy();
            }
            $('.myCustomClass').DataTable({
                "pageLength": 100
            });
            $(".alert").fadeTo(5000, 0).slideUp(500, function() {
                $(this).remove();
            });
        });
    </script>
@endpush
