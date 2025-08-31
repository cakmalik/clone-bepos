@extends('layouts.app')
@push('styles')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            border: 1px solid #ddd;
        }

        th {
            /* height: 70px; */
            font-size: 12px !important;
        }

        .sorting {
            background-color: #f5f5f5;
        }
    </style>
@endpush
@section('page')
    <div class="container-xl">
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <livewire:sales-overview />
            </div>
        </div>
    </div>
@endsection
