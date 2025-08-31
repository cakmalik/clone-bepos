<div class="modal modal-blur fade" id="consolidation-inventory" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gudang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-between">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input class="form-check-input mb-3" type="checkbox" value="" id="selectAll"> Pilih Semua
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group text-end mb-4">
                            <button class="btn btn-primary" id="refreshReport">Tampilkan</button>
                        </div>
                    </div>
                </div>
              
                <div class="table-responsive mb-4">
                    <table class="table card-table table-vcenter text-nowrap datatable table table-bordered table-striped table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>Nama Gudang</th>
                                <th>Type Gudang</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inventory as $inv)
                                <tr>
                                    <th>{{ $inv->name }}</th>
                                    <th>{{ Str::ucfirst($inv->type) }}</th>
                                    <th style="width: 3%">
                                        <label class="form-check rounded">
                                            <input class="form-check-input" name="inventory[]" value="{{ $inv->id }}" type="checkbox" {{ $inv->stock > 0 ? 'checked' : ''}}>
                                        </label>
                                    </th>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

               
            </div>
        </div>
    </div>
</div>

