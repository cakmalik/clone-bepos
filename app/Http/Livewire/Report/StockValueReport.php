<?php

namespace App\Http\Livewire\Report;

use Carbon\Carbon;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\Inventory;
use Livewire\WithPagination;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Cache;
use App\Jobs\GenerateStockValueReport;
use App\Models\StockValueReport as Model;

class StockValueReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $product_categories;
    public $inventories;
    public $outlets;
    public $date = null;

    public $product_category_id = null;
    public $inv_id              = null;
    public $out_id              = null;
    public $searchTerm          = '';

    public $total_stock_value     = 0;
    public $total_potential_value = 0;

    public $perPage = 100;

    public function mount()
    {
        $this->inventories        = Inventory::get(['name', 'id']);
        $this->outlets            = Outlet::get(['name', 'id']);
        $this->date               = Carbon::now()->format('Y-m-d');
        $this->product_categories = ProductCategory::orderBy('name', 'asc')->get(['name', 'id']);
    }

    public $labelName = '';
    public function updated($propertyName)
    {
        switch ($propertyName) {
            case 'inv_id':
                if ($this->inv_id) {
                    $this->labelName = Inventory::find($this->inv_id)->name;
                }

                $this->out_id = null;
                break;
            case 'out_id':
                if ($this->out_id) {
                    $this->labelName = $this->outlets->find($this->out_id)->name;
                }

                $this->inv_id = null;
                break;
            default:
                $this->labelName = '';
                break;
        }
        $this->resetPage();
    }

    public function refreshData()
    {
        GenerateStockValueReport::dispatchSync('now');
    }

    protected $listeners = ['initData' => 'initData'];
    public function initData(){
        if (!Cache::has('skip_generate')) {
            Cache::put('skip_generate', true, now()->addMinutes(30));
            $this->refreshData();
        } else {
        }
    }

    public function render()
    {
        $query = Model::when($this->inv_id, fn($query) => $query->where('inventory_id', $this->inv_id))
            ->when($this->out_id, fn($query) => $query->where('outlet_id', $this->out_id))
            ->when($this->date, fn($query) => $query->where('report_date', $this->date))
            ->when($this->searchTerm, function ($query) {
                return $query->whereHas('product', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->product_category_id, function ($query) {
                $query->where('product_category_id', $this->product_category_id);
            });

        $this->total_stock_value     = (clone $query)->sum('stock_value');
        $this->total_potential_value = (clone $query)->sum('potential_value');

        // Lanjutkan dengan paginate
        $data = $query->orderBy('report_date', 'desc')->paginate($this->perPage);

        return view('livewire.report.stock-value-report', ['data' => $data]);
    }
}
