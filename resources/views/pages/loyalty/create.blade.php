<div class="modal modal-blur fade" id="product_unit-create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengaturan Loyalty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    @csrf
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Set Mode</label>
                            <select name="mode_loyalty" class="form-select" id="modeLoyalty">
                                <option value="pembelian">Pembelian Product</option>
                                <option value="total_pesanan">Total Pesanan</option>
                            </select>
                        </div>
                    </div>
                    @foreach ($loyaltyData as $loyalty)
                        <div class="row" id="minTotalRow">
                            <div class="mb-3">
                                <label for="min_transaction" class="form-label">Min Total Pesanan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" autocomplete="off" class="form-control" id="min_transaction"
                                        name="min_transaction" value="{{ $loyalty->min_transaction }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="pointRow">
                            <div class="mb-3">
                                <label for="point_plus" class="form-label">Point Yang Diperoleh</label>
                                <input type="text" autocomplete="off" class="form-control" id="point_plus"
                                    name="point_plus" value="{{ $loyalty->point_plus }}" required>
                            </div>
                        </div>
                        <div class="row" id="berlakuKelipatanRow">
                            <div class="col-12 d-flex">
                                <label for="applies_multiply" class="form-label me-3">Berlaku Kelipatan</label>
                                <div>
                                    <input type="checkbox" id="applies_multiply" name="applies_multiply"
                                        class="form-check-input" {{ $loyalty->applies_multiply ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{-- <div class="row" id="customerTypeRow">
                        <div class="mb-3">
                            <label class="form-label">Berlaku Untuk Tipe Customer</label>
                            <select name="customer_type" class="form-select">
                                <option value="semua">Semua</option>
                                <option value="total_pesanan">Total Pesanan</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="row" id="notifRow">
                        <div class="mb-3">
                            <label class="form-label">Informasi</label>
                            <input name="code" type="text" autocomplete="off" readonly placeholder="Auto-Generate"
                                class="form-control @error('code') is-invalid @enderror" id="code">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" id="updateLoyalty" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        $("#minTotalRow, #pointRow, #customerTypeRow, #notifRow, #berlakuKelipatanRow").hide();

        function toggleVisibility() {
            var selectedMode = $("#modeLoyalty").val();
            if (selectedMode === "total_pesanan") {
                $("#minTotalRow, #pointRow , #berlakuKelipatanRow ").show();
                $("#customerTypeRow, #notifRow").hide();
            } else if (selectedMode === "pembelian") {
                $("#minTotalRow, #pointRow , #berlakuKelipatanRow").hide();
                $("#customerTypeRow").show();
                $("#notifRow").show();
            } else {
                $("#minTotalRow, #pointRow, #customerTypeRow, #notifRow, #berlakuKelipatanRow").hide();
            }
        }
        $("#modeLoyalty").change(toggleVisibility);
        toggleVisibility();
    });
</script>
<script>
    $(document).ready(function() {
        $('#updateLoyalty').on('click', function() {
            var minTransaction = $('#min_transaction').val();
            var pointPlus = $('#point_plus').val();
            var appliesMultiply = $('#applies_multiply').prop('checked') ? 1 : 0;
            var loyaltyId = {{ $loyalty->id }}
            console.log(minTransaction, pointPlus, appliesMultiply, loyaltyId);
            $.ajax({
                url: '{{ route('update-loyalty') }}',
                method: 'PUT',
                data: {
                    min_transaction: minTransaction,
                    point_plus: pointPlus,
                    applies_multiply: appliesMultiply,
                    loyalty_id: loyaltyId,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    location.reload(true);
                },
                error: function(error) {
                    console.log(error.responseJSON.error);
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        formatCurrency();
        $('#min_transaction').on('input', function() {
            formatCurrency();
        });
    });
    function formatCurrency() {
        var inputValue = $('#min_transaction').val();
        if (inputValue) {
            inputValue = inputValue.replace(/[^0-9]/g, '');
            var formattedValue = inputValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            $('#min_transaction').val(formattedValue);
        }
    }
</script>
