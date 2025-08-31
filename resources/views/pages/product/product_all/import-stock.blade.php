<!-- Modal Import Stok Produk -->
<div class="modal fade" id="importStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('import.stock') }}" method="POST" enctype="multipart/form-data" id="importStockForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Stok Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Dropdown memilih jenis tujuan -->
                    <div class="mb-3">
                        <label for="destination" class="form-label">Jenis Tujuan</label>
                        <select name="destination" id="destination" class="form-control" required>
                            <option value="">-- Pilih Tujuan --</option>
                            <option value="inventory">Gudang</option>
                            <option value="outlet">Outlet</option>
                        </select>
                    </div>

                    <!-- Dropdown memilih Inventory -->
                    <div id="inventoryGroup" class="mb-3" style="display: none;">
                        <label for="destination_id_inventory" class="form-label">Gudang</label>
                        <select name="destination_inventory_id" id="destination_id_inventory" class="form-control">
                            <option value="">-- Pilih Gudang --</option>
                            @foreach ($inventories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dropdown memilih Outlet -->
                    <div id="outletGroup" class="mb-3" style="display: none;">
                        <label for="destination_id_outlet" class="form-label">Outlet</label>
                        <select name="destination_outlet_id" id="destination_id_outlet" class="form-control">
                            <option value="">-- Pilih Outlet --</option>
                            @foreach ($outlets as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('template.stock') }}" class="btn btn-outline-primary">
                        <i class="fa fa-download me-1"></i> Template
                    </a>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </div>
        </form>
    </div>
</div>