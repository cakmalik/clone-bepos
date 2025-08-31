@extends('layouts.app')

@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ $title }}
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">

                <div class="card p-4">
                    @if ($can_manage_selling_price)
                        <livewire:product.tiered-price.table />
                    @else
                        <x-alert-to-upgrade />
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
