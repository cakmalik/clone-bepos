@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        Kategori Pelanggan
                    </h2>

                    @include('pages.customer.customer_category.create')
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">

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
                            <table id="categoryCustomerTable"
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 1%">#</th>
                                        <th style="width: 5%">Kode</th>
                                        <th style="width: 5%">Nama</th>
                                        <th style="width: 5%">Deskripsi</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataCustomerCategories as $cc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $cc->code }}</td>
                                            <td>{{ $cc->name }}</td>
                                            <td>{{ $cc->description }}</td>
                                            <td class="mx-auto">
                                                @include('pages.customer.customer_category.edit')
                                                @include('pages.customer.customer_category.destroy')
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
@endsection

@push('scripts')
    <script src="{{ asset('dist/libs/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('dist/libs/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('dist/libs/datatables/js/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('dist/libs/datatables/js/buttons/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('dist/libs/datatables/js/responsive/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('dist/libs/datatables/js/responsive/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            if ($('#categoryCustomerTable').length) {
                $('#categoryCustomerTable').DataTable({
                    destroy: true,
                    processing: true,
                    pageLength: 25,
                    responsive: true,
                    lengthChange: true,
                    autoWidth: false,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Total Data: _TOTAL_",
                        paginate: {
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    columns: [{
                            searchable: false,
                            orderable: false
                        },
                        {
                            searchable: true,
                            orderable: true
                        },
                        {
                            searchable: true,
                            orderable: true
                        },
                        {
                            searchable: false,
                            orderable: false
                        },
                        {
                            searchable: false,
                            orderable: false
                        }
                    ]
                });
            } else {
                console.error('Tabel kategori pelanggan tidak ditemukan!');
            }
        });
    </script>
@endpush
