<?php

namespace App\Http\Livewire\StockMutation;

use Carbon\Carbon;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\Inventory;
use App\Models\ProductStock;
use App\Models\StockMutation;
use App\Models\StockMutationItem;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use App\Models\StockMutationInventoryOutlet;
use App\Models\StockMutationInventoryOutletItem;

class Create extends Component
{
    private $model;
    private $model_detail;
    public $mutation_category;

    public $from;
    public $to;
    public $selected_ids_product = [];
    public $selected_products = [];

    public $source_id;
    public $destination_id;

    public $sources;
    public $destinations;

    public $source_object;
    public $destination_object;

    public $stock_for = 'inventory';

    protected $listeners = [
        'productAdd' => 'productAdd',
    ];

    public function updatedMutationCategory()
    {
        $this->source_id = null;
        $this->destination_id = null;

        if ($this->mutation_category == 'inventory_to_inventory') {
            $this->sources = Inventory::all();
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $this->sources = Inventory::all();
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $this->sources = Outlet::all();
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $this->sources = Outlet::all();
        }

        $this->selected_products = [];
        $this->selected_ids_product = [];
        $this->dispatchBrowserEvent('clearSelectedProducts');
    }

    public function updatedSourceId()
    {
        if ($this->mutation_category == 'inventory_to_inventory') {
            $this->destinations = Inventory::where('id', '!=', $this->source_id)->get();
            $this->stock_for = 'inventory';
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $this->destinations = Outlet::get();
            $this->stock_for = 'inventory';
            $this->source_object = Inventory::find($this->source_id);
            $this->destination_object = Outlet::find($this->destination_id);
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $this->destinations = Outlet::where('id', '!=', $this->source_id)->get();
            $this->stock_for = 'outlet';
            $this->source_object = Outlet::find($this->source_id);
            $this->destination_object = Outlet::find($this->destination_id);
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $this->destinations = Inventory::get();
            $this->stock_for = 'outlet';
            $this->source_object = Outlet::find($this->source_id);
            $this->destination_object = Inventory::find($this->destination_id);
        }

        $this->selected_products = [];
        $this->selected_ids_product = [];
        $this->dispatchBrowserEvent('clearSelectedProducts');
    }

    public function updatedDestinationId()
    {
        if ($this->mutation_category == 'inventory_to_inventory') {
            $this->source_object = Inventory::find($this->source_id);
            $this->destination_object = Inventory::find($this->destination_id);
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $this->source_object = Inventory::find($this->source_id);
            $this->destination_object = Outlet::find($this->destination_id);
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $this->source_object = Outlet::find($this->source_id);
            $this->destination_object = Outlet::find($this->destination_id);
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $this->source_object = Outlet::find($this->source_id);
            $this->destination_object = Inventory::find($this->destination_id);
        }
    }

    public $remainingMinutes = 1;

    private function __isUnderMinutes()
    {
        $lastMutation = StockMutation::latest()->first();
        if (!$lastMutation) return false;

        $lastMutationDate = Carbon::parse($lastMutation->created_at);
        $currentDate = now();
        $diffInMinutes = $lastMutationDate->diffInMinutes($currentDate);

        if ($diffInMinutes < $this->remainingMinutes) {
            $this->remainingMinutes -= $diffInMinutes;
            return true;
        }

        return false;
    }

    private function _code(): string
    {

        $code = $this->source_object->code;

        if ($this->mutation_category == 'inventory_to_inventory') {
            $code = autoCode('stock_mutations', 'code', $code . '-' . 'GG' . date('ym') . '-', 4);
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $code = autoCode('stock_mutations', 'code', $code . '-' . 'GO' . date('ym') . '-', 4);
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $code = autoCode('stock_mutations', 'code', $code . '-' . 'OO' . date('ym') . '-', 4);
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $code = autoCode('stock_mutations', 'code', $code . '-' . 'OG' . date('ym') . '-', 4);
        }

        return $code;
    }

    public function save()
    {
        if ($this->__isUnderMinutes()) {
            $this->emitUp('show-error', "Lakukan mutasi kembali setelah $this->remainingMinutes menit!");
            return;
        }

        $this->validate(
            [
                'mutation_category' => 'required',
                'source_id' => 'required',
                'destination_id' => 'required',
                'selected_products' => 'required|array',
                'selected_products.*.qty' => 'required|numeric|gt:0',
            ],
            [
                'mutation_category.required' => 'Kategori mutasi harus dipilih!',
                'source_id.required' => 'Pilih inventori asal!',
                'destination_id.required' => 'Pilih inventori tujuan!',
                'selected_products.required' => 'Pilih produk!',
                'selected_products.*.qty.required' => 'Qty harus diisi!',
                'selected_products.*.qty.gt' => 'Qty harus lebih besar dari 0!',
            ],
        );

        foreach ($this->selected_products as $key => $product) {
            $this->validate(
                [
                    'selected_products.' . $key . '.qty' => 'lte:' . $product['current_stock'],
                ],
                [
                    'selected_products.' . $key . '.qty.lte' => 'Melewati stok!',
                ],
            );
        }

        $this->store();
    }

    public function store()
    {
        DB::beginTransaction();

        try {
            // save to main table
            $this->model = new StockMutation();
            $this->model->code = $this->_code();
            $this->model->date = date('Y-m-d H:i');

            if ($this->mutation_category == 'inventory_to_outlet') {
                $this->model->inventory_source_id = $this->source_id;
                $this->model->outlet_destination_id = $this->destination_id;
            } else if ($this->mutation_category == 'inventory_to_inventory') {
                $this->model->inventory_source_id = $this->source_id;
                $this->model->inventory_destination_id = $this->destination_id;
            } else if ($this->mutation_category == 'outlet_to_outlet') {
                $this->model->outlet_source_id = $this->source_id;
                $this->model->outlet_destination_id = $this->destination_id;
            } else if ($this->mutation_category == 'outlet_to_inventory') {
                $this->model->outlet_source_id = $this->source_id;
                $this->model->inventory_destination_id = $this->destination_id;
            }

            $this->model->creator_user_id = Auth()->user()->id;
            $this->model->status = 'open';
            $this->model->mutation_category = $this->mutation_category;
            $this->model->save();

            //    save to detail table
            foreach ($this->selected_products as $product) {
                $item = new StockMutationItem();
                $item->stock_mutation_id = $this->model->id;
                $item->product_id = $product['id'];
                $item->qty = $product['qty'];
                $item->save();
            }

            DB::commit();
            $this->emitUp('show-success', 'Mutasi berhasil dibuat.');

        } catch (\Exception $e) {
            
            Log::error($e->getMessage() . ' line ' . $e->getLine());
            DB::rollBack();
            $this->dispatchBrowserEvent('error', ['type' => 'error', 'message' => $e->getMessage()]);
        
        }
    }

    public function render()
    {
        return view('livewire.stock-mutation.create');
    }

    public $product_search;
    public $show_offcanvas = false;
    public function toggleOffCanvas()
    {
        $this->show_offcanvas = !$this->show_offcanvas;
    }

    public function closeOffcanvas()
    {
        if ($this->show_offcanvas == true) {
            $this->show_offcanvas = false;
        }
    }

    public function productAdd($product)
    {
        $this->insertToList($product);
    }

    private function insertToList($product)
    {
        if (!in_array($product['id'], $this->selected_ids_product)) {
            $this->selected_ids_product[] = $product['id'];
            $this->selected_products[] = [
                'name' => $product['name'],
                'unit' => $product['unit'],
                'id' => $product['id'],
                'code' => $product['code'],
                'qty' => 0,
                'current_stock' => $product['current_stock'],
            ];
        }
    }

    public function removeProduct($key)
    {
        unset($this->selected_ids_product[$key]);
        unset($this->selected_products[$key]);
    }
}
