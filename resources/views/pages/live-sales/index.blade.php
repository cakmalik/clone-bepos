@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet"/>
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet"/>
@endpush
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Live Sales
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                {{-- @if ($message = Session::get('error'))
                    <x-alert level="danger" message="{{ $message }}"/>
                @elseif($message = Session::get('success'))
                    <x-alert level="success" message="{{ $message }}"/>
                @endif
                @foreach ($errors->all() as $error)
                    <x-alert level="danger" message="{{ $error }}"/>
                @endforeach --}}

                <div class="card mb-5">
                    <div class="card-header">
                        <h3 class="card-title">Live Sales</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">
                            <table id="live-sales-table" class="table no-datatable table-bordered">
                                <thead>
                                <tr>
                                    <th>Sales</th>
                                    <th>Outlet</th>
                                    <th class="text-right">Penjualan</th>
                                    <th class="text-right">Omzet</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($outlets as $outlet=>$sales)
                                    <tr>
                                        <td colspan="4" class="p-2 border-0"></td>
                                    </tr>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ $sale['user_name'] }}</td>
                                            <td>{{ $sale['outlet_name'] }}</td>
                                            <td class="text-right">{{ $sale['sales'] }}</td>
                                            <td class="currency text-right">{{ $sale['omzet'] }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-blue text-white font-weight-bold">
                                        <td colspan="2">Total {{ $outlet }}</td>
                                        <td class="text-right">{{ $sales->sum('sales') }}</td>
                                        <td class="currency text-right">{{ $sales->sum('omzet') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="4" class="p-2 border-0"></td>
                                </tr>
                                <tr class="bg-blue text-white font-weight-bold">
                                    <td colspan="2">Grand Total</td>
                                    <td class="text-right">{{ $sales->sum('sales') }}</td>
                                    <td class="currency text-right">{{ $sales->sum('omzet') }}</td>
                                </tr>
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
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.currency').each(function() {
                $(this).text(generateCurrency($(this).text()));
            })
        })
    </script>
@endpush
