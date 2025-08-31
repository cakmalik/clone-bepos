<?php

namespace Database\Seeders;

use App\Models\SalesDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleDetailHppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $saleDetails = SalesDetail::where('hpp', null)
            ->select('sales_details.id','product_id', 'products.capital_price')
            ->join('products', 'product_id', 'products.id')
            ->get();

        DB::beginTransaction();

        try {
            foreach($saleDetails as $item) {
                $detail = SalesDetail::find($item->id);
                $detail->hpp = $item->capital_price;
                $detail->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
        }

        
    }
}
