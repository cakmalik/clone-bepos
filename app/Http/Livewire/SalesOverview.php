<?php

namespace App\Http\Livewire;

use App\Models\Outlet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;



class SalesOverview extends Component
{
    public $start_date;
    public $end_date;
    public $outlet;
    public $status;
    public $summary;
    public $outlet_id;

    public function mount()
    {
        $this->start_date = Carbon::today()->startOfDay()->todatestring();
        $this->end_date = Carbon::today()->endOfDay()->todatestring();
    }
    public function render()
    {
        $user = auth()->user();
    
        if ($user->role->role_name == 'SUPERADMIN') {
            $outlets = Outlet::all();
        } else {
            $outlets = Outlet::whereIn('id', getUserOutlet())->get();
        }
    
        if ($this->start_date === $this->end_date) {
            $label = Carbon::parse($this->start_date)->translatedFormat('d M Y');
        } else {
            $label = Carbon::parse($this->start_date)->translatedFormat('d M Y') . ' - ' . Carbon::parse($this->end_date)->translatedFormat('d M Y');
        }
    
        if ($user->role->role_name == 'SUPERADMIN') {
            $outletIds = Outlet::pluck('id');
        } else {
            $outletIds = getUserOutlet();
        }
    
        $data = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->join('products as p', 'sd.product_id', '=', 'p.id')
            ->leftJoin('product_categories as pc', 'p.product_category_id', '=', 'pc.id')
            ->select(
                'pc.name as product_category',
                // DB::raw('SUM(sd.qty) as total_qty'),
                DB::raw('SUM(CASE WHEN s.is_retur = 0 AND s.status = "success" THEN sd.qty ELSE 0 END) as total_qty'),

                // OMZET (setelah dikurangi diskon rata-rata)
                DB::raw('SUM(CASE 
                    WHEN s.is_retur = 0 AND s.status = "success" 
                    THEN (sd.final_price * sd.qty) - (s.discount_amount / NULLIF((SELECT COUNT(*) FROM sales_details WHERE sales_id = s.id), 0)) 
                    ELSE 0 END) as total_omzet'),

                DB::raw('SUM(CASE WHEN s.is_retur = 1 AND s.status = "retur" THEN sd.final_price * sd.qty_retur ELSE 0 END) as total_omzet_retur'),

                DB::raw('SUM(CASE WHEN s.is_retur = 0 AND s.status = "success" THEN p.capital_price * sd.qty ELSE 0 END) as total_hpp'),
                DB::raw('SUM(CASE WHEN s.is_retur = 1 AND s.status = "retur" THEN p.capital_price * sd.qty_retur ELSE 0 END) as total_hpp_retur'),

                // LABA (omzet - hpp)
                DB::raw('SUM(CASE 
                    WHEN s.is_retur = 0 AND s.status = "success" 
                    THEN ((sd.final_price * sd.qty) - (s.discount_amount / NULLIF((SELECT COUNT(*) FROM sales_details WHERE sales_id = s.id), 0))) - (p.capital_price * sd.qty)
                    ELSE 0 END) as total_profit'),

                DB::raw('SUM(CASE 
                    WHEN s.is_retur = 1 AND s.status = "retur" 
                    THEN (sd.final_price * sd.qty_retur) - (p.capital_price * sd.qty_retur) 
                    ELSE 0 END) as total_profit_retur')
            )
            ->whereIn('s.outlet_id', $outletIds)
            ->when($this->start_date != '' && $this->end_date != '', function ($query) {
                $query->whereBetween('s.sale_date', [Carbon::parse($this->start_date)->startOfDay(), Carbon::parse($this->end_date)->endOfDay()]);
            })
            ->when($this->outlet_id != '', function ($query) {
                $query->where('s.outlet_id', $this->outlet_id);
            })
            ->groupBy('pc.name')
            ->get();

        return view('livewire.sales-overview', [
            'data' => $data,
            'outlets' => $outlets, 
            'label' => $label
        ]);
    }

    public function outletChanged($value)
    {
        $this->outlet_id = (int)$value;
    }
}
