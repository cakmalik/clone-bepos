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

            <div class="card mb-5">
                <div class="card-header">
                    <h3 class="card-title">
                        {{ 'KODE RETUR : ' . $sales->sale_code }} <br>
                        {{ 'CUSTOMER : ' . $sales->customer->name }} <br>
                        {{ 'NOMINAL : ' . rupiah($sales->final_amount) }} <br>
                    </h3>
                </div>
                <div class="card-body border-bottom py-3">
                    <form action="/accounting/journal_retur_sales" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="id" value="{{ $sales->id }}">
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
                                    <label class="form-label">Nominal</label>
                                    <input type="text" class="form-control @error('nominal') is-invalid @enderror"
                                        autocomplete="off" name="nominal" id="journal_retur_nominal">
                                    <small class="text-red">Maksimal Nominal :
                                        {{ rupiah($sales->final_amount - $total_debit) }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Keterangan</label>
                                    <input type="text" autocomplete="off"
                                        class="form-control  @error('description') is-invalid @enderror" name="description">
                                </div>
                            </div>

                        </div>
                        <div class="form-group text-end">
                            @if ($sales->final_amount == $total_debit)
                                <button type="submit" class="btn btn-primary" disabled><i class="fa fa-save"></i>&nbsp;
                                    Simpan</button>
                            @else
                                <div class="form-group text-end">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;
                                        Simpan</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>



            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>

                    <div class="card-body border-bottom py-3">
                        <form action="/accounting/journal_retur_sales/finish" method="post">
                            @csrf
                            <div class="form-group text-end">
                                <input type="hidden" name="id" value="{{ $sales->id }}">
                                <input type="hidden" name="journal_number_id" value="{{ $journal_number->id }}">
                                @if ($sales->final_amount != $total_debit)
                                    <button type="submit" class="btn btn-success" disabled><i
                                            class="fa fa-check"></i>&nbsp;
                                        Selesaikan Jurnal Retur</button>
                                @else
                                    <button type="submit" class="btn btn-success"><i class="fa fa-check"></i>&nbsp;
                                        Selesaikan Jurnal Retur</button>
                                @endif
                            </div>
                        </form>
                        <br>

                        <table
                            class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Retur</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($journal_number != null)
                                    @foreach ($journal_number->journalTransaction as $jn)
                                        <tr>
                                            <td>{{ $journal_number->code }}</td>
                                            <td>{{ $journal_number->date }}</td>
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
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="3" class="text-end">Total</td>
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
@endsection
@push('scripts')
    <script>
        let total_debit = '{{ $total_debit }}';
        let nominal = '{{ $sales->final_amount }}'

        let max = parseInt(nominal) - parseInt(total_debit);
        $('#journal_retur_nominal').on('keyup', function() {
            let value = $(this).val().replace(/[^,\d]/g, '').toString();;
            if (value > max) {
                $(this).val(' ')
            }
        });

        journal_retur_nominal.addEventListener('keyup', function(e) {
            journal_retur_nominal.value = formatRupiah(this.value, 'Rp.');
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
    </script>
@endpush
