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

            @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}" />
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}" />
            @endif

            <div class="row row-deck row-cards">
                <div class="card mb-5">

                    <div class="card-header">
                        <h3 class="card-title">
                            {{ $adjustment->code . ' ' . dateWithTime($adjustment->so_date) }} <br>
                            {{ rupiah($adjustment->adjustment_detail_sum_adjustment_nominal_value) }}
                        </h3>

                    </div>
                    <div class="card-body border-bottom py-3">
                        <form action="/accounting/journal_adjustment" method="post">
                            @csrf
                            <div class="row">
                                <input type="hidden" name="id" value="{{ $adjustment->id }}">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Debit</label>
                                        <select name="journal_account_debit"
                                            class="form-control @error('journal_account_debit') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih Debit
                                                &mdash;
                                            </option>

                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach

                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Kredit</label>
                                        <select name="journal_account_kredit"
                                            class="form-control @error('journal_account_kredit') is-invalid @enderror">
                                            <option selected value="0" disabled> &mdash; Pilih Kredit
                                                &mdash;
                                            </option>

                                            @foreach ($jurnal_account as $jc)
                                                <option value="{{ $jc->id }}">
                                                    {{ $jc->name }}</option>
                                            @endforeach

                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan</label>
                                        <input type="text" autocomplete="off"
                                            class="form-control  @error('description') is-invalid @enderror"
                                            name="description">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nominal</label>
                                        <input type="text" class="form-control @error('nominal') is-invalid @enderror"
                                            autocomplete="off" name="nominal" id="journal_adjustment_nominal">
                                        <small class="text-red">Maksimal Nominal :
                                            {{ rupiah(abs($adjustment->adjustment_detail_sum_adjustment_nominal_value) - abs($total_debit)) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-end">
                                @if (abs($adjustment->adjustment_detail_sum_adjustment_nominal_value) == abs($total_debit))
                                    <button type="submit" class="btn btn-primary disabled"><i class="fa fa-save"></i>&nbsp;
                                        Simpan</button>
                                @else
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                        Simpan</button>
                                @endif

                            </div>

                        </form>


                        <hr>
                        <strong>Detail Produk Adjustment</strong>

                        <div class="table-responsive mt-3">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Qty System</th>
                                        <th>Qty</th>
                                        <th>Selisih</th>
                                        <th>Qty Adjustment</th>
                                        <th>Qty Setelah di Adjustment</th>
                                        <th>Nominal Adjustment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($opnameDetail as $p)
                                        <tr>
                                            <td>{{ $p->product->name }}</td>
                                            <td>{{ $p->qty_system }}</td>
                                            <td>{{ $p->qty_real }}</td>
                                            <td>{{ $p->qty_selish }}</td>
                                            <td>{{ $p->qty_adjustment }}</td>
                                            <td>{{ $p->qty_after_adjustment }}</td>
                                            <td>{{ rupiah(abs($p->adjustment_nominal_value)) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <strong>Jurnal Adjustment </strong>
                        <div class="form-group text-end">
                            @include('pages.accounting.journal_adjustment.finish')
                        </div>
                        <div class="table-responsive mt-3">
                            <table
                                class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Akun</th>
                                        <th>Keterangan</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($journal_number != null)
                                        @foreach ($journal_number->journalTransaction as $jn)
                                            <tr>
                                                <td>{{ $jn->journalAccount->name }}</td>
                                                <td>{{ $jn->description }}</td>
                                                <td>
                                                    @if ($jn->type == 'debit')
                                                        {{ rupiah($jn->nominal) }}
                                                    @else
                                                        {{ ' ' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($jn->type == 'credit')
                                                        {{ rupiah($jn->nominal) }}
                                                    @else
                                                        {{ ' ' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($jn->type == 'debit')
                                                        <a class="btn btn-danger btn-sm py-2 px-3"
                                                            onclick="adjustmentDetail({{ $jn->id }})">
                                                            <li class="fas fa-trash"></li>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="2" class="text-end">Total</td>
                                            <td>{{ rupiah($total_debit) }}</td>
                                            <td>{{ rupiah($total_kredit) }}</td>
                                        </tr>
                                    @endif

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
    <script>
        let total_debit = @json($total_debit);
        let adj = @json($adjustment);

        let max = parseInt(Math.abs(adj.adjustment_detail_sum_adjustment_nominal_value)) - parseInt(Math.abs(total_debit));
        $('#journal_adjustment_nominal').on('keyup', function() {
            let value = $(this).val().replace(/[^,\d]/g, '').toString();;
            if (value > max) {
                $(this).val(' ')
            }
        });




        journal_adjustment_nominal.addEventListener('keyup', function(e) {
            journal_adjustment_nominal.value = formatRupiah(this.value, 'Rp.');
        });



        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);


            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }


        function adjustmentDetail(id) {
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/accounting/journal_adjustment') }}" + '/' + id,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Sukses di Hapus !', data.message, 'success').then(function() {
                                location.reload();
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error').then(function() {
                                location.reload();
                            });
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };
    </script>
@endpush
