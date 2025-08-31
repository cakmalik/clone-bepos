<?php

namespace App\Http\Livewire\Inventory\StockOpname;

use Carbon\Carbon;
use App\Models\Outlet;
use App\Models\Product;
use Livewire\Component;
use App\Models\Inventory;
use App\Models\StockOpname;
use App\Models\ProductStock;
use App\Models\ProfilCompany;
use App\Models\StockOpnameDetail;
use Illuminate\Support\Facades\DB;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class Create extends Component
{
    public $stock_opname_id = null;
    public $edit_form = false;

    public $inventories;
    public $outlets;

    public $inventory_id;
    public $outlet_id;

    public $selected_ids_product = [];
    public $selected_products = [];

    public $selected_type = 'inventory';
    public $product_search = '';

    public $perPage = 20;

    public $show_offcanvas = false;
    public $component_show = 'product';
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

    protected $listeners = ['productAdd' => 'productAdd', 'productAddCollective' => 'productAddCollective'];
    public function productAdd($product)
    {
        $this->insertToList($product);
    }

    public function productAddCollective($products)
    {
        foreach ($products as $key => $value) {
            $this->insertToList($value);
        }
    }

    private function insertToList($product)
    {
        $this->is_there_changes = true;
        if (!in_array($product['id'], $this->selected_ids_product)) {
            $this->selected_ids_product[] = $product['id'];
            $this->selected_products[] = [
                'name' => $product['name'],
                'unit' => $product['unit'],
                'id' => $product['id'],
                'code' => $product['code'],
                'qty' => 0,
            ];
        }
    }

    public function mount($so_id = null)
    {
        if ($so_id == null) {
            $this->resetAll();
        }

        if ($so_id) {
            $this->setData($so_id);
        }

        $isSuperAdmin = auth()->user()->role->role_name;

        if ($isSuperAdmin != 'SUPERADMIN') {
            $this->inventories = Inventory::whereIn('id', getUserInventory())->get();
        } else {
            $this->inventories = Inventory::all();
        }

        $this->inventory_id = $this->inventories->first()?->id;

        if ($isSuperAdmin != 'SUPERADMIN') {
            $this->outlets = Outlet::whereIn('id', getUserOutlet())->get();
        } else {
            $this->outlets = Outlet::all();
        }

        $this->outlet_id = $this->outlets->first()?->id;
    }




    public function setData($so_id)
    {
        $stockOpname = StockOpname::where('id', $so_id)->first();
        if ($stockOpname) {
            $this->stock_opname_id = $stockOpname->id;
            $this->edit_form = true;
            $this->selected_type = $stockOpname->inventory_id ? 'inventory' : 'outlet';
            $this->inventory_id = $stockOpname->inventory_id;
            $this->outlet_id = $stockOpname->outlet_id;

            $stockOpnameDetails = StockOpnameDetail::where('stock_opname_id', $stockOpname->id)->get();
            foreach ($stockOpnameDetails as $sod) {
                $this->selected_ids_product[] = $sod->product_id;
                $this->selected_products[] = [
                    'name' => $sod->product->name,
                    'unit' => $sod->product->productUnit->name,
                    'id' => $sod->product_id,
                    'code' => $sod->product->code,
                    'qty' => $sod->qty_so,
                ];
            }
        }
    }
    public function render()
    {
        return view('livewire.inventory.stock-opname.create');
    }

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function updatedSelectedProducts($value, $key)
    {
        $this->is_there_changes = true;
    }

    // deteksi perubahahn
    public function removeProduct($key)
    {
        $this->is_there_changes = true;
        unset($this->selected_ids_product[$key]);
        unset($this->selected_products[$key]);
    }

    public function save($is_done = false)
    {
        DB::beginTransaction();
        try {
            if ($this->edit_form && $this->stock_opname_id) {
                StockOpnameDetail::where('stock_opname_id', $this->stock_opname_id)->forceDelete();
            }

            $code = '';
            $inventory_id = null;
            $outlet_id = null;

            if ($this->selected_type == 'inventory') {
                $inventory = Inventory::find($this->inventory_id);
                $config = configCodeStockOpname($inventory->code);
                $inventory_id = $this->inventory_id;
            } else {
                $outlet = Outlet::find($this->outlet_id);
                $config = configCodeStockOpname($outlet->code);
                $outlet_id = $this->outlet_id;
            }
            $code = IdGenerator::generate($config);

            // update or create stock op
            $stock_opname = $this->stock_opname_id && $this->edit_form ? StockOpname::find($this->stock_opname_id) : new StockOpname();
            $stock_opname->code = $code;
            $stock_opname->so_date = Carbon::now();
            $stock_opname->outlet_id = $outlet_id;
            $stock_opname->inventory_id = $inventory_id;
            $stock_opname->user_id = Auth()->user()->id;
            $stock_opname->status = $is_done ? 'selesai' : 'belum_selesai';
            $stock_opname->save();

            foreach ($this->selected_products as $sp) {
                $qty = $sp['id'] . '_qty';

                $config = configCodeStockOpnameDetail($sp['code']);
                $codeDetail = IdGenerator::generate($config);

                StockOpnameDetail::create([
                    'code' => $codeDetail,
                    'stock_opname_id' => $stock_opname->id,
                    'product_id' => $sp['id'],
                    'qty_system' => 0,
                    'qty_so' => $sp['qty'],
                    'qty_selisih' => 0,
                ]);
            }

            DB::commit();
            $this->stock_opname_id = $stock_opname->id;
            // jika berhasil maka skrg jadikan edit form
            $this->edit_form = true;
            $this->is_there_changes = false;
            if (!$is_done) {
                $this->dispatchBrowserEvent('updated', ['message' => 'Berhasil disimpan']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage() . '' . $e->getLine());
        }
    }

    // deteksi perubahahn
    public $is_there_changes = false;
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['selected_type', 'inventory_id', 'outlet_id'])) {
            $this->is_there_changes = true;
        }
    }

    public $confirm_finish_modal = false;
    public function confirmFinish()
    {
        $this->validate([
            'selected_type' => 'required',
            'selected_products' => 'required',
        ]);
        $this->confirm_finish_modal = true;
    }

    public function closeConfirmFinish()
    {
        $this->confirm_finish_modal = false;
    }

    public function done()
    {
        $this->save(true);
        $this->emitUp('done');
    }

    public function resetAll()
    {
        $this->reset();
        $this->resetErrorBag();
    }

    public function finish()
    {
        DB::beginTransaction();

        try {
            StockOpname::where('id', $this->stock_opname_id)->update([
                'status' => 'selesai',
            ]);

            $stockOpname = StockOpname::query()
                ->where('id', $this->stock_opname_id)
                ->with('stockOpnameDetails.product', 'inventory', 'outlet', 'user')
                ->first();

            foreach ($stockOpname->stockOpnameDetails as $op) {
                if ($stockOpname->inventory_id) {
                    $productStock = ProductStock::query()
                        ->where('inventory_id', $stockOpname->inventory_id)
                        ->where('product_id', $op->product_id)
                        ->first();
                } else {
                    $productStock = ProductStock::query()
                        ->where('outlet_id', $stockOpname->outlet_id)
                        ->where('product_id', $op->product_id)
                        ->first();
                }

                if ($productStock) {
                    StockOpnameDetail::query()
                        ->where('stock_opname_id', $this->stock_opname_id)
                        ->where('product_id', $op->product_id)
                        ->update([
                            'qty_system' => $productStock->stock_current,
                            'qty_selisih' => $op->qty_so - $productStock->stock_current,
                        ]);
                } else {
                    StockOpnameDetail::query()
                        ->where('stock_opname_id', $this->stock_opname_id)
                        ->where('product_id', $op->product_id)
                        ->update([
                            'qty_system' => 0,
                            'qty_selisih' => $op->qty_so,
                        ]);
                }
            }

            $company = ProfilCompany::where('status', 'active')->first();
            DB::commit();
            $this->emitUp('done');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withWarning('Gagal di Selesaikan!');
        }
    }
}
