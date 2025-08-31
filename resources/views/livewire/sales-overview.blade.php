<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="card">
                <div class="col m-3 border-bottom border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title text-uppercase fw-bold">Ringkasan Penjualan</h3>
                        <p class='text-muted text-sm fw-bold'>{{ $label }}</p>
                    </div>
                </div>
                <div class="card-body border-bottom">
                    <div class="row align-bottom">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start">Mulai</label>
                                <input type="date" class="form-control" name="start_date" id="start_date"
                                    wire:model="start_date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end">Sampai</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" wire:model="end_date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="outlet">Outlet</label>
                                <select name="outlet" id="outlet" class="form-select" wire:model="outlet_id"
                                    wire:change="outletChanged($event.target.value)">
                                    @if(auth()->user()->role->role_name == 'SUPERADMIN')
                                        <option value="-">-- Semua Outlet --</option>
                                    @endif
                                    @foreach ($outlets as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                                    @endforeach
                                </select>
            
                            </div>
                        </div>
                        <div class="col-md mt-auto">
                            <div class="d-flex justify-content-start gap-1">
                                {{-- <div class="dropdown">
                                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Export
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <li><a class="dropdown-item" wire:click="export('excel')">Excel</a></li>
                                        <li><a class="dropdown-item"wire:click="export('pdf')">Pdf</a>
                                        </li>
                                    </ul>
                                </div> --}}
                                <a href="" type="button" class="btn btn-danger btn-block">Reset</a>
                            </div>
                        </div>
                        <div class="col-md-2 mt-auto">
                        </div>
                    </div>
                    <div class="row align-bottom mt-3">
                        <div class="col">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>KATEGORI</th>
                                        <th>QTY</th>
                                        <th>OMSET</th>
                                        <th>HPP</th>
                                        <th>LABA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total_qty = 0;
                                        $total_omset = 0;
                                        $total_profit = 0;
                                        $total_hpp = 0;
                                    @endphp
                            
                                    @forelse ($data as $i)
                                        @php
                                            $total_qty += $i->total_qty;
                                            $total_omset += $i->total_omzet;
                                            $total_hpp += $i->total_hpp;
                                            $total_profit += $i->total_profit;
                                        @endphp
                                        <tr>
                                            <th class="text-uppercase">{{ $i->product_category }}</th>
                                            <td>{{ formatDecimal($i->total_qty) }}</td>
                                            <td>Rp{{ number_format($i->total_omzet) }}</td>
                                            <td>Rp{{ number_format($i->total_hpp) }}</td>
                                            <td>Rp{{ number_format($i->total_profit) }}</td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                                @if ($data->count() > 0)
                                    <tfoot>
                                        <tr class="bg-secondary text-light fs-3">
                                            <th>TOTAL</th>
                                            <th>{{ formatDecimal($total_qty) }}</th>
                                            <th>Rp{{ number_format($total_omset) }}</th>
                                            <th>Rp{{ number_format($total_hpp) }}</th>
                                            <th>Rp{{ number_format($total_profit) }}</th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
