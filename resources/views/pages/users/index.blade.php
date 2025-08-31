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
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        Pengguna
                    </h2>
                    <a href="{{ route('users.create') }}" class="btn btn-primary ms-auto mb-2">
                        <i class="fa-solid fa-plus"></i> &nbsp; Pengguna</a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
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
                        <div class="table-responsive">
                            <table id="data-table"
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover"
                                style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Outlet</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($getUsers as $users)
                                        <tr>
                                            <td><span class="text-muted">{{ $loop->iteration }}</span></td>
                                            <td>{{ $users->users_name }}</td>
                                            <td>{{ $users->username }}</td>
                                            <td>{{ $users->role?->role_name }}</td>
                                            <td>{{ $users->outlets->pluck('name')->implode(', ') }}</td>
                                            <td>
                                                {{-- <a href="{{ route('outlet.detail',$users->id) }}" class="btn btn-info btn-sm py-2 px-3" >
                                                            <li class="fas fa-eye"></li>
                                                        </a> --}}
                                                <div class="btn-group" role="group">
                                                    <div class="btn-list">
                                                        <a href="{{ route('users.edit', $users->id) }}"
                                                            class="btn btn-outline-dark">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <div class="btn-list">
                                                        <a href="{{ route('users.inventory', $users->id) }}"
                                                            class="btn btn-outline-primary" title="inventory">
                                                            <i class="fas fa-warehouse"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="btn-group" role="group">
                                                    <div class="btn-list">
                                                        <a href="{{ route('users.outlet', $users->id) }}"
                                                            class="btn btn-outline-success" title="outlet">
                                                            <i class="fas fa-home"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @if ($users->id != 1 && $users->id != auth()->id())
                                                    @include('pages.users.destroy')
                                                @endif
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
    </div>
    </div>
@endsection
@push('scripts')
@endpush
