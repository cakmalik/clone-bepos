@if (abs($adjustment->adjustment_detail_sum_adjustment_nominal_value) == abs($total_debit))
    <a href="" style="margin-left:80%" class="btn btn-secondary btn-sm py-2 px-3" data-bs-toggle="modal"
        data-bs-target="#journal_adjustment_modal">
        <i class="fa-solid fa-save"></i> &nbsp; Selesai
    </a>
@else
    <a href="" style="margin-left:80%" class="btn btn-secondary btn-sm py-2 px-3 disabled" data-bs-toggle="modal"
        data-bs-target="#journal_adjustment_modal">
        <i class="fa-solid fa-save"></i> &nbsp; Selesai
    </a>
@endif



<div class="modal modal-blur fade" id="journal_adjustment_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Menyelesaikan Jurnal Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4><strong>Apakah yakin menyelesaikan jurnal adjustment ini?</strong>
                </h4>
            </div>
            <div class="modal-footer">
                <div class="btn-group">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <form action="/accounting/journal_adjustment/finish" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $adjustment->id }}">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Selesai</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
