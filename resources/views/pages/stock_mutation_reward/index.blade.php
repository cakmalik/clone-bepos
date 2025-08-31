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
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

                <div class="form-group text-end">
                    <a href="/stock_mutation_reward/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Buat Mutasi Hadiah
                    </a>
                </div>

                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}" />
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}" />
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}" />
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">

                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="stock-mutation-reward-table"
                                        class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Kode Mutasi Hadiah</th>
                                                <th>Tanggal</th>
                                                <th>Tipe</th>
                                                <th>Keterangan</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($reward as $r)
                                                <tr>
                                                    <td>{{ $r->code }}</td>
                                                    <td>{{ $r->formatted_date }}</td>
                                                    <td>{{ $r->type }}</td>
                                                    <td>{{ $r->description }}</td>
                                                    <td>
                                                        <a href="/stock_mutation_reward/{{ $r->id }}"
                                                            class="btn btn-info btn-sm py-2 px-3">
                                                            <li class="fas fa-eye"></li>
                                                        </a>
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
@endsection
