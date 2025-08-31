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
                        Produk Price
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                @include('pages.product.product_price.create')

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
                        <h3 class="card-title">Price</h3>
                    </div>
                    <div class="card-body border-bottom py-3">
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Code Product</th>
                                        <th>Name</th>
                                        <th>Capital Price</th>
                                        <th>Price</th>
                                        <th>Type</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($productPrices as $pp)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pp->Product->code }}</td>
                                        <td>{{ $pp->Product->name }}</td>
                                        <td>Rp. {{ number_format($pp->product->capital_price,2,',','.') }}</td>
                                        <td>Rp. {{ number_format($pp->price,2,',','.')}}</td>
                                        @if ($pp->type == 'utama')
                                            <td><span class="badge bg-green">Utama</span></td>
                                        @else
                                            <td><span class="badge bg-yellow">Lain</span></td>
                                        @endif

                                        <td>
                                            @include('pages.product.product_price.edit')
                                            @include('pages.product.product_price.destroy')
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

@endsection
@push('scripts')
<script src="{{ asset('jquery.mask.min.js') }}" ></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#price1').mask('#.##0', {reverse: true});
        $('#price2222').mask('#.##0', {reverse: true});
    });
</script>
@endpush
