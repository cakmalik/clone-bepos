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
                    <h2 class="page-title text-uppercase">
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">


            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif --}}

            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID RETUR</th>
                                        <th>Referensi</th>
                                        <th>Tanggal</th>
                                        <th>Outlet</th>
                                        <th>Customer</th>
                                        <th>Nominal Retur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $s)
                                        <tr>
                                            <td>
                                                <a
                                                    href="/accounting/journal_retur_sales/{{ $s->id }}">{{ $s->sale_code }}</a>
                                            </td>
                                            <td>{{ $s->ref_code }}</td>
                                            <td>{{ $s->sale_date }}</td>
                                            <td>{{ $s->outlet->name }}</td>
                                            <td>{{ $s->customer->name }}</td>
                                            <td>{{ rupiah($s->final_amount) }}</td>
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
@endsection
