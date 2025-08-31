<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchase_order-supplier-create">
            <i class="fas fa-search me-2"></i>
            Supplier
        </a>
    </div>
    <div class="modal modal-blur fade" id="purchase_order-supplier-create" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered " role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table
                            class="table card-table table-vcenter text-nowrap datatable table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat</th>
                                    <th>No.Handphone </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplier as $sp)
                                    <tr>
                                        <td style="width: 5%">{{ $sp->code }}</td>
                                        <td style="width: 10%">{{ $sp->name }}</td>
                                        <td style="width: 10%">{{ $sp->address }}</td>
                                        <td style="width: 10%">{{ $sp->phone }}</td>
                                        <td style="width: 3%">
                                            {{-- <input type="radio" class="form-check-input" name="supplier_id"
                                                data-name="{{ $sp->name }}" data-id="{{ $sp->id }}"
                                                id="supplier_id"> --}}
                                            <button type="button" class="btn btn-outline-primary btn-sm supplier-button rounded-2 px-3"
                                                data-id="{{ $sp->id }}">
                                                <i class="fas fa-check me-1 icon-check" style="display: none;"></i>
                                                <span class="button-text">Pilih</span>
                                            </button>
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
