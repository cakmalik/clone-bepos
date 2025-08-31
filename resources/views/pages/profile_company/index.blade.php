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
                        Profil Perusahaan
                    </h2>
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
                            <table id="data-table" class="table card-table table-vcenter text-nowrap" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="w-1">No.</th>
                                        <th>Nama Perusahaan</th>
                                        <th>Email</th>
                                        <th>No Telp</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataAllProfilCompany as $key => $pc)
                                        <tr>
                                            <td><span
                                                    class="text-muted">{{ $dataAllProfilCompany->firstItem() + $key }}</span>
                                            </td>
                                            <td>{{ $pc->name }}</td>
                                            <td>{{ $pc->email }}</td>
                                            <td>{{ $pc->telp }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <div class="btn-list">
                                                        <a href="{{ route('profileCompany.edit', $pc->id) }}"
                                                            class="btn btn-outline-dark">
                                                            <li class="fas fa-edit"></li>
                                                        </a>
                                                    </div>
                                                </div>
                                                {{-- @include('pages.profile_company.destroy') --}}
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
