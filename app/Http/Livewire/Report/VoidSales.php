<?php

namespace App\Http\Livewire\Report;

use App\Models\Sales;
use App\Models\SalesDetail;
use Livewire\Component;
use Livewire\WithPagination;

class VoidSales extends Component
{

    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $start_date, $end_date;

    public function mount()
    {
        $this->start_date = now()->toDateString();
        $this->end_date = now()->toDateString();
    }

    public function render()
    {
        $data = Sales::with(['user', 'outlet'])
            ->leftJoin('sales_details', 'sales.id', '=', 'sales_details.sales_id')
            ->where('sales.status', 'void')
            ->when(auth()->user()->role->role_name !== 'SUPERADMIN', function ($query) {
                $query->whereIn('sales.outlet_id', getUserOutlet());
            })
            ->when($this->start_date, fn($query) => $query->whereDate('sales.created_at', '>=', $this->start_date))
            ->when($this->end_date, fn($query) => $query->whereDate('sales.created_at', '<=', $this->end_date))
            ->paginate();
    
        return view('livewire.report.void-sales', compact('data'));
    }
    

    public function export(string $type)
    {
        // if ($type == 'pdf') {
        //     return redirect()->route('reportVoidPrint', [
        //         'start_date' => $this->start_date,
        //         'end_date' => $this->end_date
        //     ]);
        // } else {
        //     return redirect()->route('report.void-sales-excel', [
        //         'start_date' => $this->start_date,
        //         'end_date' => $this->end_date
        //     ]);
        // }
    }
}
