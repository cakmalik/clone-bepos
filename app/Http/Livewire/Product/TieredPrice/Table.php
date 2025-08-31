<?php

namespace App\Http\Livewire\Product\TieredPrice;

use App\Models\Outlet;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Table extends Component
{
    protected $listeners = ['close'];

    public function close()
    {
        $this->form_open = false;
        $this->product = null;
        $this->outlet_id = null;
    }

    public $search = '';
    public $filter = 'tiered';
    public $perPage = 20;

    public $form_open = false;

    public $count = 0;

    public $selected_id;
    public $product;
    public $outlet_id;

    public $as_new_price = false;

    public $filter_outlet;
    public $outlets;
    public function mount()
    {
        $this->outlets = Outlet::get();
    }

    public function select($id, $outlet_id = null)
    {
        $this->as_new_price = false;
        $this->product = Product::find($id);
        $this->outlet_id = $outlet_id;
        $this->form_open = true;
    }

    public function updatedSearch()
    {
        if ($this->search == '') {
            $this->filter = 'tiered';
        } else {
            $this->filter = 'all';
        }
    }

    public function increment()
    {
        $this->count++;
    }

    public function loadMore()
    {
        $this->perPage += 20;
        $this->render();
    }

    public function render()
    {
        $tiers = Product::with('tieres')
            // ->where('outlet_id', getOutletActive()->id)
            ->when($this->filter == 'tiered', function ($query) {
                $query->whereHas('tieres');
                // ->when($this->filter_outlet != '',  function ($q) {
                //     $q->where('outlet_id', $this->filter_outlet);
                // });
            })
            ->when($this->filter == 'non_tier', function ($query) {
                $query->whereDoesntHave('tieres');
            })
            ->when($this->filter_outlet != '',  function ($q) {
                $q->whereHas('tieres', function ($q) {
                    $q->where('outlet_id', $this->filter_outlet);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('barcode', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByDesc('updated_at');

        $this->count = $tiers->count();
        $tieredPrices = $tiers->paginate($this->perPage);

        return view('livewire.product.tiered-price.table', [
            'data' => $tieredPrices,
        ]);
    }

    public function addNewPrice($id)
    {
        $this->product = Product::find($id);
        $this->as_new_price = true;
        $this->form_open = true;
    }
}
