<?php

namespace App\Http\Livewire\Inventory\StockOpname;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\StockOpname;
use Livewire\WithPagination;

class Table extends Component
{
    protected $listeners = ['done'];

    public $perPage = 50;

    public $status = 'selesai';
    public $start_date = '';
    public $end_date = '';

    public function done()
    {
        $this->status = 'selesai';
        $this->form = false;
        $this->dispatchBrowserEvent('done-so');
    }

    public function mount()
    {
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->format('Y-m-d');
    }
    public function render()
    {
        $opname = StockOpname::with('inventory', 'outlet', 'user')
            ->where('status', '!=', 'adjustment')
            ->where(function ($query) {
                $query->whereIn('outlet_id', getUserOutlet())
                    ->orWhereIn('inventory_id', getUserInventory());
            })
            ->orderByDesc('id')
            ->where('status', $this->status)
            ->where(function ($query) {
                if ($this->start_date != '' && $this->end_date != '') {
                    $query->where([['so_date', '>=', startOfDay($this->start_date)], ['so_date', '<=', endOfDay($this->end_date)]]);
                }
            });

        $opname = $opname->paginate($this->perPage);
        // $opname->paginate($this->perPage);

        return view('livewire.inventory.stock-opname.table', ['data' => $opname]);
    }

    public function loadMore()
    {
        $this->perPage += 50;
    }

    public $form = false;
    public function create()
    {
        $this->form = true;
        $this->stock_opname_id = null;
    }

    public function toggleForm()
    {
        $this->form = !$this->form;
    }

    public $stock_opname_id = null;
    public function select($id)
    {
        $this->stock_opname_id = $id;
        $this->form = true;
    }

    public function print($id)
    {
        return redirect('/stock_opname_preview/' . $id);
    }
}
