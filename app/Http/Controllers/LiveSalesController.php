<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveSalesController extends Controller
{
    public function index()
    {
        $sales = Sales::select('users.username as user_name', 'outlets.name as outlet_name', DB::raw('count(sales.id) as sales'), DB::raw('sum(sales.final_amount) as omzet'))
            ->leftJoin('users', 'users.id', '=', 'sales.user_id')
            ->leftJoin('outlets', 'sales.outlet_id', '=', 'outlets.id')
            ->where('sales.status', '=', 'success')
            ->where('sales.is_retur', '=', false)
            ->whereRaw('date(sales.sale_date) = curdate()')
            ->groupBy('outlet_id')
            ->get();

        $outlets = collect($sales)->groupBy('outlet_name');
        return view('pages.live-sales.index', compact('sales', 'outlets'));
    }
}
