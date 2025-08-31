<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Sales;
use App\Models\Outlet;
use Livewire\Component;
use App\Exports\SalesExport;
use App\Models\Setting;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SalesTable extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $start_date;
    public $end_date;
    public $outlet;
    public $status;
    public $shipping_status;
    public $summary;
    public $users;
    public $selectedUser;
    public $cashier;
    public $selectedCashier;
    public $return;
    public $revenue;

    protected $listeners = [
        'resetFilters' => 'resetFilters'
    ];

    public function mount()
    {
        $this->start_date = session('filter.start_date', Carbon::today()->toDateString());
        $this->end_date = session('filter.end_date', Carbon::today()->toDateString());
        $this->outlet = session('filter.outlet', '-');
        $this->cashier = session('filter.cashier', '-');
        $this->status = session('filter.status', '');

        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $this->cashier = DB::table('users')
                ->select('users.id', 'users.users_name')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->where([['roles.role_name', '=', 'KASIR'], ['users.deleted_at', null]])
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->users_name,
                    ];
                });
        } else {
            $this->cashier = DB::table('users')
                ->select('users.id', 'users.users_name')
                ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
                ->join('user_outlets', 'users.id', '=', 'user_outlets.user_id')
                ->where([['roles.role_name', '=', 'KASIR'], ['users.deleted_at', null]])
                ->whereIn('user_outlets.outlet_id', getUserOutlet())
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'name' => $row->users_name,
                    ];
                });
        }
    }


    public $perPage = 100;
    public function render()
    {
        $query = Sales::query()
            ->with('salesDetails', 'customer', 'outlet')
            ->whereNot('sales_type', 'join-child')
            ->orderByDesc('id');

        //select data value pada name show_and_change_order_status
        $is_shipping = Setting::where('name', 'show_and_change_order_status')->first()->value;

        if (auth()->user()->role->role_name != 'SUPERADMIN') {
            $query->whereIn('outlet_id', getUserOutlet());
        }

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('sale_date', [Carbon::parse($this->start_date)->startOfDay(), Carbon::parse($this->end_date)->endOfDay()]);
        }

        if ($this->outlet && $this->outlet !== '-') {
            $query->where('outlet_id', $this->outlet);
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        if ($this->selectedCashier && $this->selectedCashier !== '-') {
            $query->where('user_id', $this->selectedCashier);
        }

        if ($this->status != '') {
            $query->when($this->status == 'retur', function ($q) {
                $q->whereNotNull('ref_code');
            });

            $query->when($this->status == 'success', function ($q) {
                $q->where('status', 'success');
                $q->whereNull('ref_code');
            });

            $query->when($this->status == 'draft', function ($q) {
                $q->where('status', 'draft');
            });

            $query->when($this->status == 'void', function ($q) {
                $q->where('status', 'void');
            });
        }

        if ($this->shipping_status != '') {
            $query->where('shipping_status', $this->shipping_status);
        }

        // Total penjualan sukses (tidak retur) dari sales_details
        $totalSales = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->where('s.status', 'success')
            ->where('s.is_retur', false)
            ->sum(DB::raw('sd.final_price * sd.qty'));

        // Total retur dari sales_details
        $totalReturns = DB::table('sales_details as sd')
            ->join('sales as s', 'sd.sales_id', '=', 's.id')
            ->where('s.status', 'retur')
            ->where('s.is_retur', true)
            ->sum(DB::raw('sd.final_price * sd.qty_retur'));

        $totalDiscount = DB::table('sales')
            ->where('status', 'success')
            ->where('is_retur', false)
            ->sum('discount_amount');
        

        $query->where('is_retur', false);
        $data = $query->paginate($this->perPage);
        
        $this->summary = number_format($totalSales - $totalReturns - $totalDiscount, 0, ',', '.');
        $this->return = number_format($totalReturns, 0, ',', '.');
        $this->revenue = number_format($totalSales - $totalReturns - $totalDiscount, 0, ',', '.');

        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            $outlets = Outlet::all();
        } else {
            $outlets = Outlet::whereIn('id', getUserOutlet())->get();
        }

        return view('livewire.sales-table', [
            'data' => $data,
            'outlets' => $outlets,
            'is_shipping' => $is_shipping
        ]);
    }

    public function loadMore()
    {
        $this->perPage += 100;
    }

    public function updated($propertyName)
    {
        session()->put("filter.{$propertyName}", $this->$propertyName);
    }

    public function outletChanged()
    {
        $this->render();
        session()->put('filter.outlet', $this->outlet);

    }
    public function statusChanged()
    {
        $this->render();
        session()->put('filter.status', $this->status);
    }

    public function export($type)
    {
        $query = Sales::query()
            ->with('salesDetails', 'customer', 'outlet')
            ->whereNot('sales_type', 'join-child')
            ->whereNot('is_retur', 1)
            ->orderByDesc('id');

        // Cek apakah pengguna adalah SUPERADMIN
        if (auth()->user()->role->role_name != 'SUPERADMIN') {
            // Jika bukan SUPERADMIN, filter berdasarkan outlet yang dapat diakses oleh user
            $query->whereIn('outlet_id', getUserOutlet());
        }

        if ($this->start_date && $this->end_date) {
            $query->whereBetween('sale_date', [Carbon::parse($this->start_date)->startOfDay(), Carbon::parse($this->end_date)->endOfDay()]);
        } else {
            $query->whereBetween('sale_date', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        }

        if ($this->outlet && $this->outlet !== '-') {
            $query->where('outlet_id', $this->outlet);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        if ($this->selectedCashier) {
            $query->where('user_id', $this->selectedCashier);
        }

        $start_date_parsed = Carbon::parse($this->start_date)->translatedFormat('d F Y');
        $end_date_parsed = Carbon::parse($this->end_date)->translatedFormat('d F Y');
        $date_merged = $start_date_parsed . ' - ' . $end_date_parsed;

        if ($type == 'pdf') {
            $pdf = PDF::loadView('export.sales-pdf', [
                'sales' => $query->get(),
                'date' => $date_merged,
                'summary' => $this->summary,
                'return' => $this->return,
                'revenue' => $this->revenue
            ])->output();
            return response()->streamDownload(fn() => print $pdf, 'filename.pdf');
        } else {
            $tgla = Carbon::parse($this->start_date)->translatedFormat('d F Y');
            $tglb = Carbon::parse($this->end_date)->translatedFormat('d F Y');
            return Excel::download(new SalesExport($query, $date_merged, $this->summary, $this->return, $this->revenue), 'laporan-penjualan ' . $tgla . '-' . $tglb . '.xlsx');
        }
    }


    public function userChanged($val)
    {
        $this->selectedCashier = $val;
        if ($val === '-') {
            $this->selectedCashier = null;
        }
    }

    public function resett()
    {
        $this->start_date = Carbon::today()->toDateString();
        $this->end_date = Carbon::today()->toDateString();
        $this->outlet = '-';
        $this->selectedCashier = '-';
        $this->status = '';
        
        session()->put('filter.start_date', $this->start_date);
        session()->put('filter.end_date', $this->end_date);
        session()->put('filter.outlet', $this->outlet);
        session()->put('filter.cashier', $this->cashier);
        session()->put('filter.status', $this->status);
        $this->render();
    }

    function getUserOutlet()
    {
        // Jika pengguna adalah SUPERADMIN, kembalikan semua outlet
        if (auth()->user()->role->role_name == 'SUPERADMIN') {
            return Outlet::all()->pluck('id')->toArray();  // Mengambil semua outlet
        }

        // Jika bukan SUPERADMIN, ambil outlet berdasarkan hubungan pengguna
        return auth()->user()->outlets->pluck('id')->toArray();  // Ambil outlet yang dapat diakses oleh user
    }
}
