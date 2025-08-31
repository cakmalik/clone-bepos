<?php

namespace App\Http\Livewire\Components\Product;

use App\Models\Product;
use Livewire\Component;
use App\Models\Inventory;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

class Search extends Component
{
    // ini reactive
    public $query;
    public $stock_for; //inventory or outlet
    // ini reactive
    public $show_offcanvas;
    // ini reactive
    public $selectedIds = [];
    public $for_id = null;

    public $product_search_open = false;
    public $perPage = 20;

    public $component_show = 'product'; //product or category
    //listener
    protected $listeners = [
        'clearSelectedProducts' => 'clearSelectedProducts',
    ];

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function render()
    {
        // Log::info($this->stock_for);
        // Log::info($this->for_id);

        $products = Product::query()
            ->select(
                'products.code',
                'products.barcode',
                'products.id',
                'products.name',
                'products.type_product',
                'brands.name as brand_name',
                'category.name as product_category',
                'suppliers.name as supplier_name',
                'stocks.stock_current as current_stock',
                'product_units.symbol as unit'
            )
            ->leftJoin('brands', 'brand_id', 'brands.id')
            ->leftJoin('product_categories as category', 'product_category_id', 'category.id')
            ->leftJoin('product_suppliers', 'product_suppliers.product_id', 'products.id')
            ->leftJoin('suppliers', 'suppliers.id', 'product_suppliers.supplier_id')
            ->leftJoin('product_stocks as stocks', 'stocks.product_id', 'products.id')
            ->leftJoin('inventories as inventory', 'inventory.id', 'stocks.inventory_id')
            ->leftJoin('product_units', 'product_unit_id', 'product_units.id')
            ->whereNull('stocks.deleted_at')
            ->whereNull('products.deleted_at')
            ->where(function ($query) {
                if (count($this->selectedIds) > 0) {
                    $query->whereNotIn('products.id', $this->selectedIds);
                }
            })
            ->where(function ($query) {
                if ($this->query != '') {
                    $query->where(function ($q) {
                        $q->where('products.name', 'LIKE', '%' . $this->query . '%')
                            ->orWhere('products.barcode', 'LIKE', '%' . $this->query . '%');
                    });
                }
                if ($this->stock_for == 'inventory') {
                    $query->where('stocks.inventory_id', $this->for_id)
                        ->where('stocks.stock_current', '>', 0);
                }
                if ($this->stock_for == 'outlet') {
                    $query->where('stocks.outlet_id', $this->for_id)
                        ->where('stocks.stock_current', '>', 0);
                }
            })
            // ->whereIn('products.outlet_id', getUserOutlet())
            ->groupBy('products.id')
            ->orderBy('products.updated_at')
            ->paginate($this->perPage);


        $categories = ProductCategory::whereHas('products')->get();

        return view('livewire.components.product.search', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function addProduct($product)
    {
        $this->emitUp('productAdd', $product);
    }

    public function clearSelectedProducts()
    {
        $this->selectedIds = [];
    }

    public function addCategory($category_id)
    {
        $products = Product::with('productUnit')->where('product_category_id', $category_id)->get(['name', 'id', 'code', 'product_unit_id'])
            ->map(function ($row) {
                $row->unit = $row->productUnit?->symbol ?? '-';
                return $row;
            })
            ->toArray();
        $this->emitUp('productAddCollective', $products);
    }
}
