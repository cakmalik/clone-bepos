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
                        Merk Produk
                    </h2>

                    @include('pages.brand.create')
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
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($brand as $b)
                                        <tr>
                                            <td>{{ $b->name }}</td>
                                            <td>
                                                <a href="#" class="btn btn-outline-dark btn-brand"
                                                    data-id="{{ $b->id }}" data-name="{{ $b->name }}">
                                                    <li class="fas fa-edit"></li>
                                                </a>
                                                <a onclick="BrandDelete({{ $b->id }})"
                                                    class="btn btn-outline-danger">
                                                    <li class="fas fa-trash"></li>
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
    @include('pages.brand.edit')
@endsection
@push('scripts')
    <script>
        $('.btn-brand').click(function() {
            // Get data attributes from the clicked button
            var brandId = $(this).data('id');
            var brandName = $(this).data('name');
            // Set data attributes to the modal inputs
            $('#brandName').val(brandName);

            // Set form action to include the brand ID for updating
            $('#form-brand-edit').attr('action', '/brand/' + brandId);

            // Show the modal
            $('#brand-edit').modal('show');
        });


        function BrandDelete(id) {

            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/brand') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                                Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                    location.reload();
                                });
                            }

                            ,
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                location.reload();
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi di hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
