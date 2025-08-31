<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Setting;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function receipt($receipt_code)
    {
        $sal = Sales::firstWhere('receipt_code', $receipt_code);
        if (!$sal) abort(404);
        $sal->sale_date = $sal->formatted_sale_date;
        $sal->load([
            'salesDetails' => function ($query) {
                $query->where('status', 'success');
            },
            'customer',
            'outlet',
            'paymentMethod',
            'table',
            'user',
            'bank',
        ]);
        $sal->powered_by = config('app.powered_by');
        $sal->custom_footer = Setting::where('name', 'custom_footer')->first()?->desc;

        return view('receipt.index', compact('sal'));
    }
}
