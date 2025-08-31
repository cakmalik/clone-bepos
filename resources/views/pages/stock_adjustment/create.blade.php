<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stock_adjustment-create">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Adjustment
        </a>
    </div>
    <div class="modal modal-blur fade" id="stock_adjustment-create" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adjustment Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Kode Opname</th>
                                    <th>Tanggal</th>
                                    <th>Nama Gudang</th>
                                    <th>Nama Outlet</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stock_opname as $so)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $so->code }}</td>
                                        <td>{{ $so->so_date }}</td>
                                        @if ($so->inventory_id)
                                            <td>{{ $so->inventory->name }}</td>
                                        @else
                                            <td>-</td>
                                        @endif
                                        @if ($so->outlet_id)
                                            <td>{{ $so->outlet->name }}</td>
                                        @else
                                            <td>-</td>
                                        @endif

                                        <td>
                                            <form action="{{ route('stockAdjustment.adjustment') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $so->id }}">
                                                <button type="submit" class="btn btn-primary">+</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
