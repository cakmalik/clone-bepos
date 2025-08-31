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
                        Penerimaan Pembelian
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
            @endif
            @foreach ($errors->all() as $error)
                <x-alert level="danger" message="{{ $error }}" />
            @endforeach --}}
            <div class="row row-deck row-cards">

                @if ($purchase->purchase_invoice_id != null)

                <div class="card">
                    <div class="pt-3 px-3">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="mb-0">Pembayaran Faktur</h4>
                            <div class="d-flex gap-2">
                                <a href="#" class="btn btn-outline-primary">
                                    <i class="fa fa-print me-2"></i> Cetak
                                </a>
                            </div>
                        </div>
                    </div>
                     
                    
                    <div class="card-body pb-0">
                        <div class="row">
                            {{-- Card: No. Tagihan --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">No. Tagihan</h6>
                                        <h3 class="font-weight-bold text-primary">
                                            {{ $purchaseInvoice->invoice_number }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Card: Jumlah Tagihan --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Jumlah Tagihan</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            Rp {{ number_format($purchaseInvoice->total_invoice ?? 0, 0, ',', '.') }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                    
                            {{-- Card: Tanggal Penerimaan --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Tanggal Penerimaan</h6>
                                        <h3 class="font-weight-bold text-dark">
                                            {{ \Carbon\Carbon::parse($purchaseInvoice->invoice_date)->translatedFormat('d F Y H:i') }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                    
                            {{-- Card: Status --}}
                            <div class="col-md-3 mb-3">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Status</h6>
                                        <span class="badge px-3 py-2 rounded text-uppercase 
                                            {{ $purchaseInvoice->is_done == 1 ? 'bg-success-lt text-success' : 'bg-orange-lt text-orange' }}">
                                            {{ $purchaseInvoice->is_done == 1 ? 'Lunas' : 'Belum Lunas' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                @endif

                <div class="card mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-home-7" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                    role="tab" tabindex="-1">Summary</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tabs-profile-7" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    role="tab">Detail</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tabs-home-7" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12 text-end">
                                        <button class="btn btn-outline-primary" onclick="printPageAreaSummary()">
                                            <i class="fa fa-print me-2"></i> Cetak
                                        </button>
                                    </div>
                                    <div class="col-lg-12">
                                        <div id="loadingProgress"></div>
                                        <iframe id="printframeSummary" name="printframe"
                                            style="width: 100%; height: 1500px; border:none;"></iframe>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tabs-profile-7" role="tabpanel">

                                <div class="row mb-4">
                                    <div class="col-md-12 text-end">
                                        <button class="btn btn-outline-primary" onclick="printPageAreaDetail()">
                                            <i class="fa fa-print me-2"></i>Cetak
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div id="loadingProgress"></div>
                                        <iframe id="printframeDetail" name="printframe"
                                            style="width: 100%; height: 1500px; border:none;"></iframe>
                                    </div>
                                </div>
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

        let urlNotaSummary = app_url + '/purchase_reception_summary/' + codeTransaction + '/';

        $(function() {
            $('#printframeSummary').attr('src', urlNotaSummary);
        });

        function printPageAreaSummary() {
            frame = document.getElementById("printframeSummary");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }


        let urlNotaDetail = app_url + '/purchase_reception_detail_nota/' + codeTransaction + '/';

        $(function() {
            $('#printframeDetail').attr('src', urlNotaDetail);
        });

        function printPageAreaDetail() {
            frame = document.getElementById("printframeDetail");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
