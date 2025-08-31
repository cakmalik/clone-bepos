<div class="container-xl">
    <div class="row row-deck row-cards">
        @if ($is_table)
            <div class="form-group text-end">
                <button wire:click="$set('is_table', false)" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Mutasi
                </button>
            </div>
        @endif


        <div class="card mb-5">
            <div class="card-header">
                @if (!$is_table)
                    <div class="row align-items-center">
                        <div class="d-flex align-items-center">
                            @if (!$is_table)
                                <button class="btn btn-primary me-2 text-center" wire:click='$set("is_table", true)'>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 12l14 0" />
                                        <path d="M5 12l6 6" />
                                        <path d="M5 12l6 -6" />
                                    </svg>
                                </button>
                            @endif

                            <h3>
                                @if ($is_table)
                                    Riwayat Mutasi Stok
                                @else
                                    Mutasi Stok
                                @endif
                                </h2>
                        </div>
                    </div>
                @else
                    <div class="">
                        <ul class="nav nav-tabs  card-header-tabs">
                            <li class="nav-item">
                                <a href="#" 
                                class="nav-link @if($mutation_category == 'inventory_to_outlet') active fw-semibold text-primary @endif"
                                wire:click.prevent="$set('mutation_category', 'inventory_to_outlet')">
                                    Gudang ke Outlet
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" 
                                class="nav-link @if($mutation_category == 'inventory_to_inventory') active fw-semibold text-primary @endif"
                                wire:click.prevent="$set('mutation_category', 'inventory_to_inventory')">
                                    Gudang ke Gudang
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" 
                                class="nav-link @if($mutation_category == 'outlet_to_outlet') active fw-semibold text-primary @endif"
                                wire:click.prevent="$set('mutation_category', 'outlet_to_outlet')">
                                    Outlet ke Outlet
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" 
                                class="nav-link @if($mutation_category == 'outlet_to_inventory') active fw-semibold text-primary @endif"
                                wire:click.prevent="$set('mutation_category', 'outlet_to_inventory')">
                                    Outlet ke Gudang
                                </a>
                            </li>
                        </ul>
                    </div>

                @endif
            </div>

            @if ($is_table)
                <div class="card-body my-3">
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
                            <label class="form-control-label">Status</label>
                            <select id="filter-status" class="form-control filter-stock-mutation" name="status"
                                wire:model='status'>
                                <option value="" selected>SEMUA</option>
                                <option value="draft">DRAFT</option>
                                <option value="open">BELUM DISETUJUI</option>
                                <option value="done">DISETUJUI</option>
                                <option value="void">DITOLAK</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="stock-mutation-table"
                                    class="table no-datatable card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Kode Mutasi</th>
                                            <th style="width: 15%;">Tanggal</th>
                                            <th style="width: 20%;">Dari</th>
                                            <th style="width: 20%;">Ke</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 5%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mutations as $i)
                                            {{-- @dd($i); --}}
                                            <tr>
                                                <td>
                                                    <a href="{{ route('stockMutation.print', $i->id) }}">{{ $i->code }}</a>
                                                </td>
                                                <td x-on:click="">
                                                    {{ \Carbon\Carbon::parse($i->date)->format('d/m/Y H:i') }}
                                                </td>
                                                <td x-on:click="">{{ $i->source_name }}</td>
                                                <td x-on:click="">{{ $i->destination_name }}</td>
                                                <td x-on:click="">
                                                    <span
                                                        class="badge bg-{{ mutation_color($i->status) }}-lt text-uppercase">{{ getMutationStatus($i->status) }}</span>
                                                </td>
                                                <td>
                                                    @if ($i->status == 'open')
                                                        <div class="btn-group">
                                                            @if (
                                                                ($i->outlet_destination_id && in_array($i->outlet_destination_id, $user_outlet_ids)) ||
                                                                    ($i->inventory_destination_id && in_array($i->inventory_destination_id, $user_inventory_ids)))
                                                                <button type="button" class="btn btn-outline-primary"
                                                                    wire:click="confirm({{ $i->id }})"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#confirmationMutation">
                                                                    <i class="fa fa-check"></i>
                                                                </button>
                                                            @endif

                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">Tidak ada data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <x-load-more :data="$mutations" />
                            </div>
                        </div>
                    </div>

                    <div wire:ignore.self class="modal fade" id="confirmationMutation" tabindex="-1"
                        aria-labelledby="modalTitle" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="modal-status bg-primary"></div>
                                <div class="modal-body text-center py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-primary icon-lg"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 9v2m0 4v.01" />
                                        <path
                                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                                    </svg>
                                    <h3>Konfirmasi Persetujuan Mutasi</h3>
                                    <div class="text-secondary">
                                        <p>Apakah Anda yakin ingin menyetujui mutasi ini? Setelah disetujui, perubahan
                                            tidak dapat dibatalkan.</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="w-100">
                                        <div class="row">
                                            <div class="col">
                                                <button class="btn w-100" data-bs-dismiss="modal"
                                                    wire:click="rejectMutation">Tolak
                                                    Mutasi</button>
                                            </div>
                                            <div class="col">
                                                <button class="btn btn-primary w-100" data-bs-dismiss="modal"
                                                    wire:click="acceptMutation"> Setujui Mutasi </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="card-body border-bottom py-3">
                    <livewire:stock-mutation.create />
                </div>
            @endif

        </div>

    </div>
</div>

<script>
    Livewire.on('show-success', message => {
        alert(message); // atau gunakan Toastify, Swal, dll
    })

    Livewire.on('show-error', message => {
        alert(message); // sesuaikan
    })
</script>

