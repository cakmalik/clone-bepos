<div class="col-auto ms-auto d-print-none">
    <div class="btn-list">
        <a href="javascript:void(0)" class="btn btn-primary d-none" id="btnBonusProduct" data-bs-toggle="modal"
            data-bs-target="#bonusProductModal">
            <i class="fas fa-plus me-2"></i>
            Bonus
        </a>
    </div>
</div>

<div class="modal modal-blur fade" id="bonusProductModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dataTable"
                        class="table card-table table-vcenter text-nowrap w-100 datatable table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                            <tr>
                                <td>{{ $item->barcode }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">
                                    <button type="button" 
                                            id="items_bonus"
                                            class="btn btn-outline-primary select-product" 
                                            data-name="{{ $item->name }}" 
                                            data-id="{{ $item->id }}">
                                        <i class="fa fa-plus"></i>
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
