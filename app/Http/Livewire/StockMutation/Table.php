<?php

namespace App\Http\Livewire\StockMutation;

use Carbon\Carbon;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\Inventory;
use App\Models\ProductStock;
use App\Models\StockMutation;
use Illuminate\Support\Facades\DB;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\StockMutationInventoryOutlet;

class Table extends Component
{
    // state
    public $is_table = true;
    public $mutation_category = 'inventory_to_outlet';

    private $model;
    // prop
    public $start_date;
    public $end_date;
    public $type;
    public $status;

    public $sources;
    public $destinations;

    public $source_id;
    public $destination_id;

    public $user_outlet_ids;
    public $user_inventory_ids;

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');

        $user = auth()->user();

        $this->user_outlet_ids = auth()->user()->userOutlets->pluck('outlet_id')->toArray();
        $this->user_inventory_ids = auth()->user()->userInventories->pluck('inventory_id')->toArray();
    }

    protected $listeners = ['show-success' => 'showSuccess', 'show-error' => 'showError'];

    /**
     * When mutation created, reset is_table state to true and fire a success browser event.
     *
     * @return void
     */
    public function showSuccess($message)
    {
        $this->is_table = true;
        $this->dispatchBrowserEvent('success', ['type' => 'success', 'message' => $message]);
    }

    public function showError($message)
    {
        $this->is_table = true;
        $this->dispatchBrowserEvent('error', ['type' => 'error', 'message' => $message]);
    }


    public function loadMore()
    {
        $this->perPage += 50;
    }

    public $perPage = 50;

    public function render()
    {
        $myInventoriesId = array_map(function ($item) {
            return $item['id'];
        }, collect(Auth::user()->inventories)->toArray());

        $mutations = StockMutation::with([
            'inventorySource',
            'inventoryDestination',
            'outletSource',
            'outletDestination'
        ])
        ->when($this->start_date, function ($query) {
            $query->whereDate('date', '>=', $this->start_date);
        })
        ->when($this->end_date, function ($query) {
            $query->whereDate('date', '<=', $this->end_date);
        })
        ->when($this->mutation_category, function ($query) {
            $query->where('mutation_category', $this->mutation_category);
        })
        ->when($this->status, function ($query) {
            $query->where('status', $this->status);
        })
        ->orderBy('date', 'desc')
        ->paginate($this->perPage);

        return view('livewire.stock-mutation.table', ['mutations' => $mutations]);
    }

    public $model_id;

    public function confirm($id)
    {
        $this->model_id = $id;
    }

    public function acceptMutation()
    {
        $model = StockMutation::findOrFail($this->model_id);

        $product_ids = $model->items->pluck('product_id')->toArray();

        // cek stok
        foreach ($product_ids as $product_id) {
            $productItem = $model->items->where('product_id', $product_id)->first();
            $qty = $productItem->qty;

            $query = ProductStock::where('product_id', $product_id);

            // Cek mutation_category untuk sumber stok
            if ($model->mutation_category == 'inventory_to_inventory' || $model->mutation_category == 'inventory_to_outlet') {
                $query->where('inventory_id', $model->inventory_source_id);
            } elseif ($model->mutation_category == 'outlet_to_outlet' || $model->mutation_category == 'outlet_to_inventory') {
                $query->where('outlet_id', $model->outlet_source_id);
            }

            $stock = $query->first();

            if (!$stock || $stock->stock_current < $qty) {
                Log::error("Gagal menerima mutasi. Stok produk '{$productItem->product->name}' tidak mencukupi.");
                $this->emit('showError', "Gagal menerima mutasi. Stok produk '{$productItem->product->name}' tidak mencukupi.");

                return;
            }
        }

        // jika stok cukup, lanjutkan update status mutasi
        $model->status = 'done';
        $model->approved_user_id = auth()->user()->id;
        $model->updated_at = now();
        $model->save();

        // Set source dan destination sesuai kategori mutasi
        if ($model->mutation_category == 'inventory_to_outlet') {
            $this->source_id = $model->inventory_source_id;
            $this->destination_id = $model->outlet_destination_id;

            $this->sources = Inventory::find($this->source_id);
            $this->destinations = Outlet::find($this->destination_id);
        } elseif ($model->mutation_category == 'inventory_to_inventory') {
            $this->source_id = $model->inventory_source_id;
            $this->destination_id = $model->inventory_destination_id;

            $this->sources = Inventory::find($this->source_id);
            $this->destinations = Inventory::find($this->destination_id);
        } elseif ($model->mutation_category == 'outlet_to_outlet') {
            $this->source_id = $model->outlet_source_id;
            $this->destination_id = $model->outlet_destination_id;

            $this->sources = Outlet::find($this->source_id);
            $this->destinations = Outlet::find($this->destination_id);
        } elseif ($model->mutation_category == 'outlet_to_inventory') {
            $this->source_id = $model->outlet_source_id;
            $this->destination_id = $model->inventory_destination_id;

            $this->sources = Outlet::find($this->source_id);
            $this->destinations = Inventory::find($this->destination_id);
        }

        foreach ($product_ids as $product_id) {
            $this->_createMutation(
                $product_id,
                $model->items->where('product_id', $product_id)->first()->qty,
                $this->sources->id,
                $this->destinations->id,
                $model->code,
                $model->creator_user_id
            );
        }

        foreach ($product_ids as $product_id) {
            $this->_approveMutation(
                $product_id,
                $model->items->where('product_id', $product_id)->first()->qty,
                $this->sources->id,
                $this->destinations->id,
                $model->code
            );
        }

        $this->emit('show-success', 'Mutasi berhasil disetujui.');
        $this->emit('closeModal');
        $this->reset('model_id');
    }

    private function _createMutation($product_id, $qty, $source_id, $destination_id, $code, $creator_id): bool
    {
        $product_id = intval($product_id);
        $qty = intval($qty);
        $source_id = intval($source_id);
        $destination_id = intval($destination_id);

        if ($this->mutation_category == 'inventory_to_inventory') {
            $from_query = 'inventory_id';
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $from_query = 'inventory_id';
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $from_query = 'outlet_id';
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $from_query = 'outlet_id';
        } else {
            $from_query = null;
        }

        DB::beginTransaction();
        try {
            // cari stok
            $sourceProduct = ProductStock::where($from_query, $source_id)
                ->where('product_id', $product_id)
                ->first();

            // kalau ketemu, update kurangi stok nya
            if ($sourceProduct) {
                $stockBefore = $sourceProduct->stock_current;
                $sourceProduct->stock_current = $sourceProduct->stock_current - $qty;

                $sourceProduct->save();
            } else {
                $this->dispatchBrowserEvent('error', ['type' => 'error', 'message' => 'Item stok yang dipilih tidak ditemukan !']);
            }

            // riwayat stok keluar
            $SourceProductStockHistory = [
                'user_id' => $creator_id,
                'product_id' => $product_id,
                'document_number' => $code,
                'history_date' => Carbon::now(),
                'stock_change' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $sourceProduct->stock_current,
                'action_type' => 'minus',
            ];

            if ($this->mutation_category == 'inventory_to_inventory') {
                $SourceProductStockHistory['inventory_id'] = $this->sources->id;
                $SourceProductStockHistory['outlet_id'] = null;
                $SourceProductStockHistory['desc'] = 'Mutasi : ' . $code . ' ke gudang ' . $this->destinations->name;
            } elseif ($this->mutation_category == 'inventory_to_outlet') {
                $SourceProductStockHistory['inventory_id'] = $this->sources->id;
                $SourceProductStockHistory['outlet_id'] = null;
                $SourceProductStockHistory['desc'] = 'Mutasi : ' . $code . ' ke outlet ' . $this->destinations->name;
            } elseif ($this->mutation_category == 'outlet_to_outlet') {
                $SourceProductStockHistory['inventory_id'] = null;
                $SourceProductStockHistory['outlet_id'] = $this->sources->id;
                $SourceProductStockHistory['desc'] = 'Mutasi : ' . $code . ' ke outlet ' . $this->destinations->name;
            } elseif ($this->mutation_category == 'outlet_to_inventory') {
                $SourceProductStockHistory['inventory_id'] = null;
                $SourceProductStockHistory['outlet_id'] = $this->sources->id;
                $SourceProductStockHistory['desc'] = 'Mutasi : ' . $code . ' ke gudang ' . $this->destinations->name;
            }

            ProductStockHistory::create($SourceProductStockHistory);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getLine());
            DB::rollBack();
            return false;
            $this->dispatchBrowserEvent('error', ['type' => 'error', 'message' => $e->getMessage() . ' ' . $e->getLine()]);
        }
    }

    private function _approveMutation($product_id, $qty, $source_id, $destination_id, $code): bool
    {
        $product_id = intval($product_id);
        $qty = intval($qty);
        $source_id = intval($source_id);
        $destination_id = intval($destination_id);

        if ($this->mutation_category == 'inventory_to_inventory') {
            $to_query           = 'inventory_id';
        } elseif ($this->mutation_category == 'inventory_to_outlet') {
            $to_query           = 'outlet_id';
        } elseif ($this->mutation_category == 'outlet_to_outlet') {
            $to_query           = 'outlet_id';
        } elseif ($this->mutation_category == 'outlet_to_inventory') {
            $to_query           = 'inventory_id';
        } else {
            $to_query = null;
        }

        DB::beginTransaction();
        try {
            // ketika approve maka mulai dari sini untuk menambahkan qty ke tujuannya
            $productStock = ProductStock::where($to_query, $destination_id)
                ->where('product_id', $product_id)
                ->first();

            if ($productStock) {
                $stockBefore = $productStock->stock_current;
                $productStock->stock_current += $qty;
                $productStock->save();
            } else {
                $stockBefore = 0;

                $productStockData = [
                    'product_id' => $product_id,
                    'stock_current' => $qty,
                ];

                if ($this->mutation_category == 'inventory_to_inventory') {
                    $productStockData['inventory_id'] = $destination_id;
                    $productStockData['outlet_id'] = null;
                } elseif ($this->mutation_category == 'inventory_to_outlet') {
                    $productStockData['outlet_id'] = $destination_id;
                    $productStockData['inventory_id'] = null;
                } elseif ($this->mutation_category == 'outlet_to_outlet') {
                    $productStockData['outlet_id'] = $destination_id;
                    $productStockData['inventory_id'] = null;
                } elseif ($this->mutation_category == 'outlet_to_inventory') {
                    $productStockData['inventory_id'] = $destination_id;
                    $productStockData['outlet_id'] = null;
                }

                $productStock = ProductStock::create($productStockData);
            }

            //riwayat stok masuk
            $DestinationProductStockHistory = [
                'user_id' => Auth()->user()->id,
                'product_id' => $product_id,
                'document_number' => $code,
                'history_date' => Carbon::now(),
                'stock_change' => $qty,
                'stock_before' => $stockBefore,
                'stock_after' => $productStock->stock_current,
                'action_type' => 'plus',
            ];

            if ($this->mutation_category == 'inventory_to_inventory') {
                $DestinationProductStockHistory['inventory_id'] = $destination_id;
                $DestinationProductStockHistory['outlet_id'] = null;
                $DestinationProductStockHistory['desc'] = 'Mutasi : ' . $code . ' dari gudang ' . $this->sources->name;
            } elseif ($this->mutation_category == 'inventory_to_outlet') {
                $DestinationProductStockHistory['inventory_id'] = null;
                $DestinationProductStockHistory['outlet_id'] = $destination_id;
                $DestinationProductStockHistory['desc'] = 'Mutasi : ' . $code . ' dari gudang ' . $this->sources->name;
            } elseif ($this->mutation_category == 'outlet_to_outlet') {
                $DestinationProductStockHistory['inventory_id'] = null;
                $DestinationProductStockHistory['outlet_id'] = $destination_id;
                $DestinationProductStockHistory['desc'] = 'Mutasi : ' . $code . ' dari outlet ' . $this->sources->name;
            } elseif ($this->mutation_category == 'outlet_to_inventory') {
                $DestinationProductStockHistory['inventory_id'] = $destination_id;
                $DestinationProductStockHistory['outlet_id'] = null;
                $DestinationProductStockHistory['desc'] = 'Mutasi : ' . $code . ' dari outlet ' . $this->sources->name;
            }

            ProductStockHistory::create($DestinationProductStockHistory);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' ' . $e->getLine());
            DB::rollBack();
            return false;
            $this->dispatchBrowserEvent('error', ['type' => 'error', 'message' => $e->getMessage() . ' ' . $e->getLine()]);
        }
    }

    public function rejectMutation()
    {
        $model = StockMutation::findOrFail($this->model_id);

        $model->status = 'void';
        $model->rejected_user_id = auth()->user()->id;
        $model->updated_at = now();
        $model->save();

        // Mutasi ditolak
        $this->emit('show-error', 'Mutasi ditolak.');
        $this->emit('closeModal');
        $this->reset('model_id');
    }

}
