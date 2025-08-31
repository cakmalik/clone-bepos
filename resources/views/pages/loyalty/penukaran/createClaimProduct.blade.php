@extends('layouts.app')
@section('page')
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Claim Point with Product
                    </h2>
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="/loyalty_point" class="btn btn-primary"> <i class="fa-solid fa-arrow-left"></i>&nbsp; Kembali</a>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        {{-- @if ($message = Session::get('error'))
                            <x-alert level="danger" message="{{ $message }}" />
                        @elseif($message = Session::get('success'))
                            <x-alert level="success" message="{{ $message }}" />
                        @endif
                        @foreach ($errors->all() as $error)
                            <x-alert level="danger" message="{{ $error }}" />
                        @endforeach --}}
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Claim Points</h3>
                                </div>
                                <form method="POST">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Nama Produk</label>
                                                    <select class="form-control @error('product_id') is-invalid @enderror"
                                                        name="product_id" id="product_id">
                                                        <option value="0" disabled selected> &mdash; Pilih Produk
                                                            &mdash;</option>
                                                    </select>
                                                    @error('product_id')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Besar Point</label>
                                                    <input type="text"
                                                        class="form-control  @error('amount') is-invalid @enderror"
                                                        name="amount" id="amount" required>
                                                    <div id="info"> </div>
                                                    @error('amount')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Qty</label>
                                                    <input type="text"
                                                        class="form-control  @error('qty') is-invalid @enderror"
                                                        name="qty" id="qty" required>
                                                    <div id="info"> </div>
                                                    @error('qty')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-end">
                                        <div class="d-flex">
                                            <button type="reset" class="btn btn-link">Reset</button>
                                            <button type="button" class="btn btn-primary ms-auto" onclick="submitForm()"><i
                                                    class="fa-solid fa-floppy-disk"></i> &nbsp; Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('jquery.mask.min.js') }}"></script>
@endpush
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#product_id').select2({
            ajax: {
                url: '{{ route('loyalty_point.searchProductLoyalty') }}', // Your server-side search endpoint
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // Search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            },
            minimumInputLength: 2 // Minimum characters before a search is performed
        });
    });
</script>
<script>
    function submitForm() {
        var formData = {
            'product_id': $('#product_id').val(),
            'amount': $('#amount').val(),
            'qty': $('#qty').val(),
            '_token': "{{ csrf_token() }}"
        };
        $.ajax({
            type: 'POST',
            url: '{{ route('loyalty_point.creteClaimProduct') }}',
            data: formData,
            dataType: 'json',
            success: function(data) {
                window.location.href = "{{ route('loyalty_point.index') }}";
            },
            error: function(error) {
                console.error(error.responseJSON.message);
            }
        });
    }
</script>
