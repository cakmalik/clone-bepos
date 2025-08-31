<div>

    @error('tiers')
        <div class="alert alert-danger">
            <span class="text-danger">{{ $message }}</span>
        </div>
    @enderror

    <div class="d-flex justify-content-between">
        <div class="d-flex gap-2">
            <button class="btn btn-default" wire:click="closeForm">
                <i class="fas fa-close me-3 "></i>
                Tutup
            </button>
            <button class="btn btn-primary" wire:click="addTier">
                <i class="fas fa-plus me-3 "></i>
                Harga Bertingkat
            </button>
        </div>
        {{-- <div class="d-flex">
           outletid: {{ $outlet_id }}
        </div>
        <div class="d-flex">
           as_new: {{ $as_new_price }}
        </div> --}}
        {{-- @if ($as_new_price) --}}
        <div class="d-flex align-items-center">
            <label for="globalOutletId" style="white-space: nowrap;" class="me-2">Harga untuk :</label>
            <select class="form-select" wire:model="globalOutletId">
                <option value="">-- Semua Outlet--</option>
                @foreach ($outlets as $outlet)
                    <option value="{{ $outlet->id }}" {{ $outlet->id == $globalOutletId ? 'selected' : '' }}>
                        {{ $outlet->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- @endif --}}

        <div class="d-flex gap-2">
            <button class="btn btn-success" wire:click="save">
                <i class="fas fa-floppy-disk me-3 "></i>
                Simpan
            </button>
        </div>
    </div>
    <hr>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}

    <div class="d-flex my-2 text-center align-items-center gap-3">
        <div class="col-md-2">
            Min
        </div>
        <div class="col-md-2">
            Max
        </div>
        <div class="col-md-4">
            Harga
        </div>
    </div>
    @foreach ($tiers as $key => $i)
        <div class="d-flex my-2 text-center align-items-center gap-3" wire:key="tier-{{ $key }}">
            <div class="col-md-2">
                <input type="number" wire:model.lazy="tiers.{{ $key }}.min" class="form-control"
                    x-mask:dynamic="number">
                @error('tiers.' . $key . '.min')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-2">
                <input type="number" wire:model.lazy="tiers.{{ $key }}.max" class="form-control"
                    x-mask:dynamic="number">
                @error('tiers.' . $key . '.max')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <input type="text" wire:model="tiers.{{ $key }}.price" class="form-control"
                    x-mask:dynamic="$money($input)">
                @error('tiers.' . $key . '.price')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            {{-- <div class="col-1 text-start">
                <div> {{ rupiah($tiers[$key]['price']) }} </div>
            </div> --}}
            <div class="col-1" wire:click="removeTier({{ $key }})">
                <button class="btn btn-danger" tabindex="-1">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    @endforeach


    <div class="d-flex justify-content-end mt-6 mb-6">
        <a class="" wire:click="toggleLog">
            Riwayat perubahan data
        </a>
    </div>

    @if ($logs && $showLog)
        <div class="table-responsive mt-6">
            <table
                class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $l)
                        @php
                            $dec = json_decode($l->properties);
                            // dd($dec);
                        @endphp
                        <tr>
                            <td>
                                {{ $l->causer?->users_name }}
                            </td>
                            <td>
                                {{ $l->event }}
                            </td>
                            <td>
                                <div class="d-flex gap-3">
                                    @if (property_exists($dec, 'attributes'))
                                        <div>
                                            <p>Attributes:</p>
                                            <ul>
                                                @foreach ($dec->attributes as $key => $value)
                                                    @if ($key === 'updated_at')
                                                        <li>{{ $key }}:
                                                            {{ \Carbon\Carbon::parse($value)->tz('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
                                                        </li>
                                                    @else
                                                        <li>{{ $key }}: {{ $value }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (property_exists($dec, 'old'))
                                        <div class="">
                                            <p>Old Values:</p>
                                            <ul>
                                                @foreach ($dec->old as $key => $value)
                                                    @if ($key === 'updated_at')
                                                        <li>{{ $key }}:
                                                            {{ \Carbon\Carbon::parse($value)->tz('Asia/Jakarta')->translatedFormat('d F Y H:i') }}
                                                        </li>
                                                    @else
                                                        <li>{{ $key }}: {{ $value }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
    window.addEventListener('updated-tier', function(e) {
        Toast.fire({
            icon: "success",
            title: e.detail.message,
            position: "top-end",
        });
    })
    window.addEventListener('logs-null', function(e) {
        Toast.fire({
            icon: "error",
            title: e.detail.message,
            position: "top-end",
        });
    })
</script>
