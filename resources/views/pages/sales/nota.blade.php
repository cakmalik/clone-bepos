@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />
@endpush

@section('page')
    <div class="container-xl">
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Kembali</a>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">KODE TRANSAKSI: {{ $transaction->sale_code }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">

                            <div class="col-md-12">
                                <div class="form-group text-right">
                                    <button class="btn btn-primary" onclick="printPageArea()">
                                        <i class="fa fa-print me-2" aria-hidden="true"></i> Cetak
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <iframe id="printframe" name="printframe"
                                    style="width: 100%; height: 1500px; border:none;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var app_url = {!! json_encode(url('/')) !!};
        let codeTransaction = '{{ $transaction->sale_code }}';

        let nota_type = 'NORMAL';
        let urlNota = app_url + '/sales-nota/' + codeTransaction + '/';

        $(function() {
            $('#printframe').attr('src', urlNota + nota_type);
        });

        $('#nota_option').change(function() {
            nota_type = $('#nota_option').val();
            $('#printframe').attr('src', urlNota + nota_type);
        });

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
