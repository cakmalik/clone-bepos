<div class="modal modal-blur fade" id="penukaran_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Penukaran Loyalty</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    @csrf
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-label">Set Mode</label>
                            <select name="mode_loyalty" class="form-select" id="ClaimPointSelect">
                                <option value="Discount">Discount</option>
                                <option value="Penukaran">Penukaran Items</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="btn-group">
                            <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" id="claimPoint" class="btn btn-primary">Next</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        $("#claimPoint").on("click", function () {
            var selectedMode = $("#ClaimPointSelect").val();
            if (selectedMode === "Discount") {
                window.location.href = "{{ route('loyalty_point.getClaimDiscount') }}";
            } else if (selectedMode === "Penukaran") {
                window.location.href = "{{ route('loyalty_point.getClaimProduct') }}";
            }
        });
    });
</script>
