<!-- Modal Import Produk -->
<div class="modal fade" id="importProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
      <form action="{{ route('import.product') }}" method="POST" enctype="multipart/form-data" id="importProductForm">
          @csrf
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Import Produk</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                  <div class="mb-3">
                      <input type="file" name="file" class="form-control" required>
                  </div>
              </div>
              <div class="modal-footer justify-content-end">
                  <a href="{{ route('template.product') }}" class="btn btn-outline-primary">
                      <i class="fa fa-download me-1"></i> Template
                  </a>
                  <button type="submit" class="btn btn-primary">Import</button>
              </div>
          </div>
      </form>
  </div>
</div>