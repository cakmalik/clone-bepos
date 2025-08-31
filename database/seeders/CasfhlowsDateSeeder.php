<?php

namespace Database\Seeders;

use App\Models\Cashflow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CasfhlowsDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::beginTransaction();

        try {
            $cashflows = Cashflow::where('transaction_code', '!=', null)
                ->select('cashflows.id', 's.sale_date')
                ->join('sales as s', 'transaction_code', 's.sale_code')
                ->get();


            foreach ($cashflows as $cashflow) {
                $cashflowUpdate = Cashflow::find($cashflow->id);
                $cashflowUpdate->transaction_date = $cashflow->sale_date;
                $cashflowUpdate->save();
            }

            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
