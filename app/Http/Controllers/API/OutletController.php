<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class OutletController extends Controller
{
    public function tables($outlet_id)
    {
        $tables = Table::where('outlet_id', $outlet_id)->orderBy('name')->get();
        return response()->json([
            'status' => 'success',
            'data' => $tables,
        ]);
    }
}
