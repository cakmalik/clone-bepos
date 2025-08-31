<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function stock()
    {
        $set = Setting::where('name', 'stock_alert')->first();
        return response()->json([
            'status' => 'success',
            'data' => $set->value == '0' ? false : true
        ], 200);
    }
}
