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
                        Jurnal Retur Pembelian
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
                        <h3 class="card-title">Jurnal Retur Pembelian</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Retur</th>
                                        <th>Tanggal</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($purchase as $p)
                                        <tr>
                                            <td>
                                                <a href="journal_retur_purchase/{{ $p->id }}">{{ $p->code }}</a>
                                            </td>
                                            <td>{{ dateWithTime($p->purchase_date) }}</td>
                                            <td>{{ $p->user->username }}</td>
                                            @if ($p->purchase_status == 'Finish')
                                                <td> <span class="badge badge-sm bg-green">Selesai</span></td>
                                            @endif
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
