@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Tambah Kas Keluar
                    </h2>
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
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Buat Kas Keluar Baru</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Kas Keluar</label>
                                <select id="input-master-id" class="form-select">
                                    <option selected value="0" disabled> &mdash; Pilih
                                        Kas &mdash;
                                    </option>
                                    @foreach ($cashMasters as $master)
                                        <option value="{{ $master->id }}">{{ $master->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Referensi</label>
                                <input id="input-ref-code" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea id="input-description" class="form-control"></textarea>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label class="form-label">Nominal</label>
                                <input type="text" class="form-control currency" data-currency-value="#nominal-value">
                                <input id="nominal-value" type="hidden">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 d-flex">
                                <button id="add-item-button" type="button" class="btn btn-success btn-block ms-auto">
                                    <i class="fas fa-plus me-2"></i>
                                    Tambahkan
                                </button>
                            </div>
                        </div>
                        <hr />
                        <form action="{{ route('cashProofOut.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <table id="items-table" class="table no-datatable">
                                        <thead>
                                            <tr>
                                                <th>Kategori Kas</th>
                                                <th>Referensi</th>
                                                <th>Keterangan</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Diterima Dari</label>
                                    <input type="text" class="form-control" name="received_from" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex">
                                    <button type="submit" class="btn btn-primary btn-block ms-auto">
                                        <i class="fas fa-save me-2"></i>
                                        Simpan
                                    </button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.currency').each(function() {
                const value_element = $($(this).data('currency-value'))
                const data_real = value_element.val() || 0
                $(this).val(generateCurrency(Number(data_real)))
                    .on('input', function() {
                        const number = parseCurrency(this.value)
                        const text = generateCurrency(number)
                        value_element.val(number)
                        $(this).val(text)
                    })
            })

            $('#input-master-id').select2()

            let items_count = 0

            $('#add-item-button').click(function(e) {
                const master_id = Number($('#input-master-id').val());
                const ref_code = $('#input-ref-code').val();
                const description = $('#input-description').val();
                const nominal = Number($('#nominal-value').val());
                if (!master_id || !ref_code || !description || !nominal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lengkapi Data',
                        text: 'Harap lengkapi data',
                    })
                    return
                }

                items_count++
                const el = `
                        <tr>
                            <td>
                                <input type="hidden" name="items[${items_count}][cash_master_id]" value="${master_id}">
                                ${$('#input-master-id option:selected').text()}
                            </td>
                            <td>
                                <input type="hidden" name="items[${items_count}][ref_code]" value="${ref_code}">
                                ${ref_code}
                            </td>
                            <td>
                                <input type="hidden" name="items[${items_count}][description]" value="${description}">
                                ${description}
                            </td>
                            <td>
                                <input type="hidden" name="items[${items_count}][nominal]" value="${nominal}">
                                ${generateCurrency(nominal)}
                            </td>
                        </tr>
                    `
                if (items_count === 1) {
                    $('#items-table tbody').html(el);
                } else {
                    $('#items-table tbody').append(el);
                }

                let selected = $('#input-master-id');
                selected.val(selected.find("option:first").val()).trigger("change");
                $('#input-ref-code').val(' ');
                $('#input-description').val(' ');
                $('.currency').val(' ');

            })
        })
    </script>
@endpush
