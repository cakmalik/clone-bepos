@extends('layouts.app')
@push('styles')
    <link href="{{ asset('dist/libs/datatables/css/dataTables.bootstrap4.css') }}" rel="stylesheet" />
    {{-- <link href="{{ asset('dist/libs/datatables/css/buttons/buttons.bootstrap4.css') }}" rel="stylesheet" /> --}}
    <link href="{{ asset('dist/libs/datatables/css/responsive/responsive.bootstrap4.css') }}" rel="stylesheet" />

    {{-- <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script> --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" /> --}}

    <style>
        .custom-table {
            margin-top: 0px;
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table th,
        .custom-table td {
            text-align: left;
            padding: 8px;
        }

        .custom-table tr:nth-child(even) {
            background-color: #f2f2f2
        }

        .custom-table th {
            background-color: #1e293b;
            color: white;
        }

        .summary {
            font-weight: bold;
            font-size: 16px;
            padding: 8px;
            border: 1px solid rgb(96 165 250);
            color: rgb(59 130 246);
            background-color: rgba(96, 165, 250, 0.2);
        }
    </style>
    <style>
        /* Style untuk card status */
        .status-card {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }
    
        .status-card:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    
        .status-card input:checked+label {
            background-color: #f8f9fa;
            border-color: #007bff;
        }
    
          /* Style untuk card status yang terpilih */
        .status-card.selected {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
    
        .status-card.selected .status-icon {
            transform: scale(1.1);
        }
    
        /* Style untuk ikon */
        .status-icon {
            transition: transform 0.3s ease;
        }
    
        .status-card:hover .status-icon {
            transform: scale(1.1);
        }
    
        /* Style untuk modal header */
        .modal-header {
            border-bottom: none;
        }
    
        .modal-title {
            font-weight: bold;
        }
    
        /* Style untuk tombol simpan */
        #saveStatus {
            transition: background-color 0.3s ease;
        }
    
        #saveStatus:hover {
            background-color: #0056b3;
        }
    </style>

    @section('page')
        <div class="container-xl">

        </div>
        <div class="page-body">
            <div class="container-xl">
                <div class="row row-deck row-cards">
                    {{-- @if ($message = Session::get('error'))
                        <x-alert level="danger" message="{{ $message }}" />
                    @elseif($message = Session::get('success'))
                        <x-alert level="success" message="{{ $message }}" />
                    @endif
                    @foreach ($errors->all() as $error)
                        <x-alert level="danger" message="{{ $error }}" />
                    @endforeach --}}
                    @include('pages.sales.card_information')

                    <livewire:sales-table />

                </div>
            </div>
        </div>
    @endsection
