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
                <div class="col">
                    <div class="d-flex align-items-center gap-2">
                        <a href="/purchase_requisition" class="btn btn-white">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <h2 class="page-title">
                            Permintaan Pembelian
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body mt-2">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">{{ $title }}</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-outline-primary" onclick="printPageArea()">
                                    <i class="fas fa-print me-2"></i>Cetak</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
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
        let app_url = {!! json_encode(url('/')) !!};
        let codeTransaction = '{{ $purchase->code }}';

        let urlNota = app_url + '/purchase_requisition_nota/' + codeTransaction + '/';

        $(function() {
            $('#printframe').attr('src', urlNota);
        });

        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
