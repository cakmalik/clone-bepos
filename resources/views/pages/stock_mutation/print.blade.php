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
                        Mutasi Stok
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
                <div class="card mb-5">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $mutation->code }}</h3>
                        <div>
                            @if ($mutation->status == 'draft')
                                <span class="badge bg-{{ mutation_color($mutation->status) }}-lt text-uppercase">Draft</span>
                            @elseif ($mutation->status == 'open')
                                <span class="badge bg-{{ mutation_color($mutation->status) }}-lt text-uppercase">Belum Disetujui</span>
                            @elseif ($mutation->status == 'done')
                                <span class="badge bg-{{ mutation_color($mutation->status) }}-lt text-uppercase">Disetujui</span>
                            @elseif ($mutation->status == 'void')
                                <span class="badge bg-{{ mutation_color($mutation->status) }}-lt text-uppercase">Ditolak</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body border-bottom py-3">
                        <div class="row mb-4">
                            <div class="col-md-12 text-end">
                                <button class="btn btn-primary" onclick="printPageArea()">
                                    <i class="fas fa-print me-2"></i>Cetak
                                </button>

                                @if ($isReceiver && $mutation->status == 'open')
                                    @include('pages.stock_mutation.confirm')
                                @endif
                            </div>

                        </div>


                        <div class="row">
                            <div class="col-lg-12">
                                <div id="loadingProgress"></div>
                                <iframe id="printframe" name="printframe"
                                    src="{{ route('stockMutation.receipt', [$mutation->id]) }}"
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
        function printPageArea() {
            frame = document.getElementById("printframe");
            framedoc = frame.contentWindow;
            framedoc.focus();
            framedoc.print();
        }
    </script>
@endpush
