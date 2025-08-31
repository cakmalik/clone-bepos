@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Laporan Mutasi Stok
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- @if ($message = Session::get('error'))
                <x-alert level="danger" message="{{ $message }}"/>
            @elseif($message = Session::get('success'))
                <x-alert level="success" message="{{ $message }}"/>
            @endif
            @foreach ($errors->all() as $error)
                <x-alert level="danger" message="{{ $error }}"/>
            @endforeach --}}

            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <span class="ml-4">Jenis Mutasi : &nbsp;</span>
                        <div class="form-check form-check-inline mt-2">
                            <input class="form-check-input" type="radio" id="gudangKeGudang" name="mutation_type" value="gudangKeGudang" checked>
                            <label class="form-check-label" for="gudangKeGudang">Gudang ke Gudang</label>
                        </div>
                        <div class="form-check form-check-inline ml-3 mt-2">
                            <input class="form-check-input" type="radio" id="gudangKeOutlet" name="mutation_type" value="gudangKeOutlet">
                            <label class="form-check-label" for="gudangKeOutlet">Gudang ke Outlet</label>
                        </div>
                        <div class="form-check form-check-inline ml-3 mt-2">
                            <input class="form-check-input" type="radio" id="outletKeOutlet" name="mutation_type" value="outletKeOutlet">
                            <label class="form-check-label" for="outletKeOutlet">Outlet ke Outlet</label>
                        </div>
                    </div>
                    
                    <div class="card-body py-3">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Mulai</label>
                                <input id="input-start-date" type="date" class="form-control" name="start_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <label class="form-label">Sampai</label>
                                <input id="input-end-date" type="date" class="form-control" name="end_date"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3" id="inventory-source-div">
                                <label class="form-label">Gudang Asal</label>
                                <select id="input-inventory-source-id" class="form-select select2" name="inventory_source_id">
                                    <option value="">Semua</option>
                                    @foreach ($inventories as $inventory)
                                        <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3" id="outlet-source-div">
                                <label class="form-label">Outlet Asal</label>
                                <select id="input-outlet-source-id" class="form-select select2" name="outlet_source_id">
                                    <option value="">Semua</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3" id="inventory-destination-div">
                                <label class="form-label">Gudang Tujuan</label>
                                <select id="input-inventory-destination-id" class="form-select select2" name="inventory_destination_id">
                                    <option value="">Semua</option>
                                    @foreach ($inventories as $inventory)
                                        <option value="{{ $inventory->id }}">{{ $inventory->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3" id="outlet-div">
                                <label class="form-label">Outlet Tujuan</label>
                                <select id="input-outlet-destination-id" class="form-select select2" name="outlet_destination_id">
                                    <option value="">Semua</option>
                                    @foreach ($outlets as $outlet)
                                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12 d-flex">
                                <div class="form-group">
                                    <button class="btn btn-primary" id="refreshReport">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-refresh" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                                        </svg>
                                        REFRESH LAPORAN
                                    </button>
                                    <button onclick="printPageArea()" class="btn btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                            <rect x="7" y="13" width="10" height="8" rx="2"></rect>
                                        </svg>
                                        CETAK
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body border-top">
                        <div class="row">
                            <div id="stock-mutation-document" class="col-12">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            loadReport();

            $('#input-inventory-source-id, #input-inventory-destination-id, #input-outlet-source-id, #input-outlet-destination-id').select2();

            $('input[name="mutation_type"]').on('change', function () {
                toggleInputFields();
            });

            function toggleInputFields() {
                const selectedType = $('input[name="mutation_type"]:checked').val();

                if (selectedType === 'gudangKeGudang') {
                    $('#input-inventory-source-id').parent().show();

                    $('#input-inventory-destination-id').parent().show();
                    $('#input-inventory-destination-id').prop('disabled', false);
                
                    $('#input-outlet-destination-id').parent().hide();
                    $('#input-outlet-destination-id').prop('disabled', true);

                    $('#input-outlet-source-id').parent().hide();
                    $('#input-outlet-source-id').prop('disabled', true);
                } else if (selectedType === 'gudangKeOutlet') {
                    $('#input-inventory-source-id').parent().show();
                    $('#input-inventory-source-id').prop('disabled', false);  
                    
                    $('#input-outlet-destination-id').parent().show();  
                    $('#input-outlet-destination-id').prop('disabled', false);
                    
                    $('#input-inventory-destination-id').parent().hide();
                    $('#input-inventory-destination-id').prop('disabled', true);

                    $('#input-outlet-source-id').parent().hide();
                    $('#input-outlet-source-id').prop('disabled', true);
                } else if (selectedType === 'outletKeOutlet') {
                    $('#input-outlet-source-id').parent().show();  
                    $('#input-outlet-source-id').prop('disabled', false);

                    $('#input-inventory-source-id').parent().hide();
                    $('#input-inventory-source-id').prop('disabled', true);  
                    
                    $('#input-outlet-destination-id').parent().show();  
                    $('#input-outlet-destination-id').prop('disabled', false);
                    
                    $('#input-inventory-destination-id').parent().hide();
                    $('#input-inventory-destination-id').prop('disabled', true);
                }

            }

            function loadReport() {
                let url = '{{ url('/report/inventory/stock_mutation/print') }}';
                const params = new URLSearchParams();
                params.append('start_date', $('#input-start-date').val());
                params.append('end_date', $('#input-end-date').val());
                params.append('inventory_source_id', $('#input-inventory-source-id').val());
                params.append('outlet_source_id', $('#input-outlet-source-id').val());

                const selectedType = $('input[name="mutation_type"]:checked').val();
                console.log('Selected Mutation Type:', selectedType); // Debugging
                if (selectedType) {
                    params.append('mutation_type', selectedType); // Pastikan mutation_type ditambahkan ke query string
                }

                // Tentukan parameter berdasarkan tipe mutasi
                if (selectedType === 'gudangKeGudang') {
                    params.append('inventory_destination_id', $('#input-inventory-destination-id').val());
                } else if (selectedType === 'gudangKeOutlet') {
                    params.append('outlet_destination_id', $('#input-outlet-destination-id').val());
                } else if (selectedType === 'outletKeOutlet') {
                    params.append('outlet_destination_id', $('#input-outlet-destination-id').val());
                }

                url += '?' + params.toString();
                console.log('Final URL:', url);
                $('#stock-mutation-document').html(`<iframe id="printframe" src="${url}" style="width: 100%; height: 1500px;"></iframe>`);
            }

            $('#refreshReport').click(loadReport);

            function printPageArea() {
                let frame = document.getElementById("printframe");
                const framedoc = frame.contentWindow;
                framedoc.focus();
                framedoc.print();
            }

            toggleInputFields();
        });

    </script>
@endpush
