@extends('layouts.app')
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
        <livewire:stock-mutation.table />
    </div>
@endsection
