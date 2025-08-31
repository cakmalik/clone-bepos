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
                        Metode Pembayaran
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @include('pages.payment_method.create') --}}
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
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Gambar</th>
                                        <th>Name Pembayaran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentMethods as $pm)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <img src="{{ asset('payment-methods/' . $pm->image) ?? asset('storage/images/' . $pm->image) }}"
                                                    alt="payment-method" width="40">
                                            </td>
                                            <td>{{ $pm->name }}</td>
                                            <td>
                                                <form action="{{ route('payment-method.toggle', $pm->id) }}" method="POST"
                                                    id="toggle-form-{{ $pm->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            onchange="document.getElementById('toggle-form-{{ $pm->id }}').submit();"
                                                            {{ $pm->is_active ? 'checked' : '' }}>
                                                    </label>
                                                </form>
                                            </td>

                                            {{-- <td>
                                            @include('pages.payment_method.edit')
                                            @include('pages.payment_method.destroy')
                                        </td> --}}
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
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
@endpush
