<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SellingPrice;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function valueReport(Request $request)
    {
        return view('pages.accounting.stock.value-report.index');
    }

    public function valueReportPrint(Request $request)
    {
        $products = Product::with('prices')->withSum('productStock', 'stock_current')->get()->toArray();
        $selling_prices = SellingPrice::all();

        foreach ($products as &$product) {
            $product['pricesObject'] = [];
            foreach ($product['prices'] as $price) {
                $product['pricesObject'][$price['selling_price_id']] = $price['price'];
            }
        }

        $data = [
            'title' => 'Laporan Nilai Stok',
            'products' => $products,
            'selling_prices' => $selling_prices,
            'company' => profileCompany(),
        ];

        return view('pages.accounting.stock.value-report.print', $data);
    }
}
