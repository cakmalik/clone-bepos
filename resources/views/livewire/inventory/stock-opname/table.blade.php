<div class="aaa">
    <div class="container-xl">
    <!-- Page title -->
        <div class="page-header d-print-none">
            <div class="row d-flex justify-content-between align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Stok Opname
                    </h2>
                </div>

                @if ($form)
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-primary rounded-2 text-center" wire:click='toggleForm'>
                        <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

                        

    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                @if (!$form)
                    <div class="d-flex justify-content-between">
                        <div class="col-4">
                            <div class="form-selectgroup">
                                <label class="form-selectgroup-item">
                                    <input type="radio" wire:model="status" value="selesai"
                                        class="form-selectgroup-input" @if ($status == 'selesai') checked @endif>
                                    <span
                                        class="form-selectgroup-label"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                        <i class="fas fa-check"></i>
                                        Selesai</span>
                                </label>

                                <label class="form-selectgroup-item">
                                    <input type="radio" wire:model="status" value="belum_selesai"
                                        class="form-selectgroup-input" @if ($status == 'selesai') checked @endif>
                                    <span
                                        class="form-selectgroup-label"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                                        <i class="fas fa-pause"></i>
                                        Belum Selesai</span>
                                </label>


                            </div>
                        </div>

                        <div class="btn-list">
                            <button wire:click='create' class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Opname
                            </button>
                        </div>
                    </div>
                    <div class="card py-3" x-cloak>
                        <div class="card-body py-3">
                            <div class="row mb-4">
                                {{-- <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select wire:model="status" id="status" class="form-select">
                                            <option value="selesai">SELESAI</option>
                                            <option value="belum_selesai">BELUM SELESAI</option>
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Mulai</label>
                                        <input type="date" class="form-control" wire:model="start_date"
                                            id="start_date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">Sampai</label>
                                        <input type="date" class="form-control" wire:model="end_date" id="end_date">
                                    </div>
                                </div>
                            </div>

                            <div class="">
                                <table
                                    class="table no-datatable card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Opname</th>
                                            <th>Tanggal</th>
                                            <th>Nama Gudang</th>
                                            <th>Nama Outlet</th>
                                            <th>Status Opname</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($data as $key=> $i)
                                            <tr wire:click="select({{ $i->id }})" style="cursor: pointer;">
                                                <td>
                                                    {{ $i->code }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($i->so_date)->translatedFormat('d M Y H:i') }}
                                                </td>
                                                <td>{{ $i->inventory?->name ?? '-' }}</td>
                                                <td>{{ $i->outlet?->name ?? '-' }}</td>
                                                <td>
                                                    @if ($i->status === 'selesai')
                                                        <span class="badge badge-sm bg-green-lt">Selesai</span>
                                                    @else
                                                        <span class="badge badge-sm bg-orange-lt">Belum Selesai</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button wire:click="print({{ $i->id }})"
                                                        class="btn btn-outline-primary"><i
                                                            class="fas fa-print"></i></button>

                                                    @if ($i->status === 'belum_selesai')
                                                        <button wire:click="select({{ $i->id }})"
                                                            class="btn btn-outline-dark"><i
                                                                class="fas fa-edit"></i></button>
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
                                <x-load-more :data="$data" />
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-5" x-cloak>
                        <div class="card-body border-bottom py-3">
                            <livewire:inventory.stock-opname.create :so_id="$stock_opname_id" />
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('done-so', function(e) {

            Toast.fire({
                icon: "success",
                title: "Berhasil menyelesaikan Stock Opname",
                position: "top-end",
            });
        })
    </script>
</div>
