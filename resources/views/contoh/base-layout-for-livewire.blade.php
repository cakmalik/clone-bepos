<div class="container-xl">
    <div class="row row-deck row-cards">
        @if ($is_table)
            <div class="form-group text-end">
                <button wire:click="$set('is_table', false)" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Mutasi Baru
                </button>
            </div>
        @endif


        <div class="card mb-5">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="d-flex">
                        @if (!$is_table)
                            <button class="btn btn-primary me-2 text-center" wire:click='$set("is_table", true)'>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                    width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M5 12l14 0" />
                                    <path d="M5 12l6 6" />
                                    <path d="M5 12l6 -6" />
                                </svg>
                            </button>
                        @endif

                        <h2 class="page-title">
                            @if ($is_table)
                                Stock Opname
                            @else
                                Buat Opname
                            @endif
                        </h2>
                    </div>
                </div>
            </div>

            @if ($is_table)
                <div class="card-body border-bottom py-3">
                    <div class="row mb-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-control-label">Mulai</label>
                            <input id="filter-start-date" type="date" name="start_date" wire:model='start_date'
                                class="form-control filter-stock-mutation">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-control-label">Sampai</label>
                            <input id="filter-end-date" type="date" name="end_date" wire:model='end_date'
                                class="form-control filter-stock-mutation">
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-control-label">Jenis</label>
                            <select id="filter-type" class="form-control filter-stock-mutation" name="type"
                                wire:model='type'>
                                <option value="" selected>SEMUA</option>
                                <option value="outgoing">KELUAR</option>
                                <option value="incoming">MASUK</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-control-label">Status</label>
                            <select id="filter-status" class="form-control filter-stock-mutation" name="status"
                                wire:model='status'>
                                <option value="" selected>SEMUA</option>
                                <option value="draft">DRAFT</option>
                                <option value="open">OPEN</option>
                                <option value="done">SELESAI</option>
                                <option value="void">VOID</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                {{-- TABLE --}}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-body border-bottom py-3">
                    <livewire.stock-mutation.create />
                </div>
            @endif

        </div>


    </div>
</div>
