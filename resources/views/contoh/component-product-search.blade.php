  <!-- BLADE CONDIITION -->

  @if ($to_id)
      <div x-data @click.outside="$wire.closeOffcanvas">
          @include('livewire.components.product.template')
          <livewire:components.product.search wire:key="{{ Str::random() }}" :show_offcanvas="$show_offcanvas" :query="$product_search"
              :selectedIds="$selected_ids_product" />
      </div>
  @endif


  <!-- LW'S Component -->

  public $show_offcanvas = false;
  public function toggleOffCanvas()
  {
  $this->show_offcanvas = !$this->show_offcanvas;
  }

  public function closeOffcanvas(){
  if($this->show_offcanvas==true){
  $this->show_offcanvas = false;
  }
  }

  protected $listeners = ['productAdd'];
  public function productAdd($product)
  {
  $this->is_there_changes = true;
  $this->selected_ids_product[] = $product['id'];
  $this->selected_products[] = [
  'name' => $product['name'],
  'id' => $product['id'],
  'code' => $product['code'],
  'qty' => 0,
  ];
  }


  {{-- parent Blade looping example  --}}
  
  <div class="row mb-3">
      <div class="col-md-7 text-center">
          <strong class="form-label text-uppercase text-bold">Nama Produk</strong>
      </div>
      <div class="col-md-2 text-center">
          <strong class="form-label text-uppercase text-bold">Stok Sekarang</strong>
      </div>
      <div class="col-md-2 text-center">
          <strong class="form-label text-uppercase text-bold">Jumlah Mutasi</strong>
      </div>
      <div class="col-md-1 text-center">
      </div>
  </div>

  @foreach ($selected_products as $key => $product)
      <div class="row mb-1" x-data wire:key='{{ $key }}'>
          <div class="col-md-7" x-on:click="$refs.productInput{{ $key }}.select(); keep_focus = true" x-cloak>
              <div for="product__{{ $key }}" class=" product-card card p-2 text-uppercase">
                  {{ $product['name'] }}</div>
          </div>
          <div class="col-md-2 text-center"
              x-on:click="$refs.productInput{{ $key }}.select(); keep_focus = true" x-cloak>
              <div for="product__{{ $key }}" class=" product-card card p-2 text-uppercase">
                  {{ $product['current_stock'] }}</div>
          </div>
          <div class="col-md-2 text-center" x-cloak>
              <input id="product__{{ $key }}" type="text" class="form-control text-center"
                  wire:model="selected_products.{{ $key }}.qty" x-ref="productInput{{ $key }}"
                  x-on:keydown.arrow-up.prevent="selectedProductIndex = selectedProductIndex > 0 ? selectedProductIndex - 1 : 0"
                  x-on:keydown.arrow-down.prevent="selectedProductIndex = selectedProductIndex < {{ count($selected_products) - 1 }} ? selectedProductIndex + 1 : {{ count($selected_products) - 1 }}"
                  x-on:keydown.enter.prevent="" x-focus="selectedProductIndex === {{ $key }}"
                  x-on:click="() => $refs.productInput{{ $key }}.select(); keep_focus = true"
                  x-mask:dynamic="number
                ">
          </div>
          <div class="col-md-1">
              <button class="btn btn-danger" wire:click="removeProduct({{ $key }})" tabindex="-1">
                  <i class="fas fa-trash"></i>
              </button>
          </div>
      </div>
  @endforeach
