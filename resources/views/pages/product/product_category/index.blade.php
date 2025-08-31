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
                        Kategori Produk
                    </h2>
                    @include('pages.product.product_category.create')
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
                            <table id="categoryTable"
                                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 5%">Kode</th>
                                        <th style="width: 5%">Nama</th>
                                        <th style="width: 5%">Batas Minimal</th>
                                        <th>Sub kategori</th>
                                        <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataProductCategories as $pc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pc->code }}</td>
                                            <td>{{ $pc->name }}</td>
                                            <td>
                                                @if ($pc->type_margin == 'PERCENT')
                                                    {{ $pc->minimum_margin ? $pc->minimum_margin . '%' : '0' }}
                                                @elseif($pc->type_margin == 'NOMINAL')
                                                    {{ $pc->minimum_margin ? 'Rp. ' . $pc->minimum_margin : '0' }}
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td style="max-width: 100px; overflow-x: auto;">
                                                {{-- <span class="badge bg-blue-lt cursor-pointer"> --}}
                                                @include('pages.product.product_category.create-sub')
                                                @foreach ($pc->children as $child)
                                                    <a href="#"
                                                        class="button-sub-category badge bg-blue-lt cursor-pointer my-1"
                                                        data-id="{{ $child->id }}" data-name="{{ $child->name }}"
                                                        data-code="{{ $child->code }}" data-bs-toggle="modal"
                                                        data-bs-target="#editSubCategory">
                                                        {{ $child->name }}
                                                    </a>
                                                    @if ($loop->iteration % 2 == 0)
                                                        <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="mx-auto">
                                                @include('pages.product.product_category.edit')
                                                @include('pages.product.product_category.destroy')
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
    <x-modal id="editSubCategory" title="Edit Sub">
        <x-slot name="body">
            <form id="editForm" method="POST">
                @method('PUT')
                @csrf
                <div class="form-group mb-3">
                    <label for="name">Nama Sub Kategori</label>
                    <input type="hidden" name="sub_category_id" id="id">
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Nama Sub Kategori">
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" class="text-danger" id="deleteButton">Hapus</a>
                    <button type="button" id="updateButton" class="btn btn-primary">Update</button>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
        </x-slot>
    </x-modal>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#categoryTable').DataTable({
                destroy: true,
                serverSide: false,
                pageLength: 25,
                columns: [
                    null,
                    {
                        "searchable": false
                    },
                    null,
                    {
                        "searchable": false
                    },
                    {
                        "searchable": false
                    },
                    {
                        "searchable": false
                    },
                ]
            });


            $('.button-sub-category').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#editSubCategory').modal('show');
                $('#editSubCategory').find('.modal-body #id').val(id);
                $('#editSubCategory').find('.modal-body #name').val(name);
            });

            $('#deleteButton').click(function(e) {
                e.preventDefault(); // Mencegah aksi default tautan

                var confirmation = confirm('Apakah Anda yakin ingin menghapus?');
                if (confirmation) {
                    var id = $('#editSubCategory').find('.modal-body #id').val();
                    var token = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '/productCategory/delete-child',
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: token,
                            sub_category_id: id
                        },
                        success: function(response) {
                            // Handle success response
                            Toast.fire({
                                icon: 'success',
                                title: response.message,
                                timer: 2000
                            });
                            // Tutup modal
                            $('#editSubCategory').modal('hide');
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
                            // Handle error response
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON.message
                            });
                            console.log(xhr);
                        }
                    });

                }
            });

            $('#updateButton').click(function(e) {
                e.preventDefault(); // Mencegah aksi default tombol submit

                var id = $('#editSubCategory').find('.modal-body #id').val();
                var name = $('#editSubCategory').find('.modal-body #name').val();
                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/productCategory/update-child',
                    type: 'POST',
                    data: {
                        _method: 'PUT',
                        _token: token,
                        sub_category_id: id,
                        name: name,
                    },
                    success: function(response) {
                        // Handle success response
                        Toast.fire({
                            icon: 'success',
                            title: response.message,
                            timer: 2000
                        })
                        //close modal
                        $('#editSubCategory').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    },
                    error: function(xhr) {
                        // Handle error response
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON.message
                        })
                        console.log(xhr);
                    }
                });
            });
        });
    </script>

    <script>
        function enableParent() {
            if (document.getElementById("is_parent_categoryy").checked) {
                document.getElementById("child_wrapper").style.display = "none";
            } else {
                document.getElementById("child_wrapper").style.display = "block";
            }
        }


        function enableParent2() {
            if (document.getElementById("is_parent_categoryyy").checked) {
                document.getElementById("parent_categoryyy").style.display = "none";
            } else {
                document.getElementById("parent_categoryyy").style.display = "block";
            }
        }
    </script>
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
@endpush
