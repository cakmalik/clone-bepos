<?php
namespace App\Jobs;

use App\Models\Inventory;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductStockHistory;
use App\Models\PurchaseDetail;
use App\Models\SalesDetail;
use App\Models\StockValueReport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateStockValueReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $when;
    public function __construct($when = null)
    {
        $this->when = $when;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Stock Value Report dibuat pada: ' . now());

        $products = Product::pluck('id');

        foreach ($products as $product) {
            // generate for outlet, generate for inventory
            $inventories = Inventory::pluck('id');
            foreach ($inventories as $inventory) {
                $this->createStockValueReport($product, $inventory, null);
            }

            $outlets = Outlet::pluck('id');
            foreach ($outlets as $outlet) {
                $this->createStockValueReport($product, null, $outlet);
            }
        }
    }

    private function createStockValueReport($product_id, $inventory_id = null, $outlet_id = null)
    {
        $today         = Carbon::today();
        $data_for_date = $this->when == 'now' ? $today : $today->subDay();

        if (! $inventory_id && ! $outlet_id) {
            return;
        }
        if ($outlet_id != null) {
            $where = 'outlet_id';
            $id    = $outlet_id;
        } elseif ($inventory_id != null) {
            $where = 'inventory_id';
            $id    = $inventory_id;
        } else {
            return;
        }

        $product = Product::with('productPrice')->where('id', $product_id)->first();

        $lastDate = ProductStockHistory::where('product_id', $product->id)
            ->where($where, $id)
            ->where('created_at', '<', $today)
            ->selectRaw('DATE(created_at) as date')
            ->orderBy('date', 'desc')
            ->first()?->date;

        // kasi kondisi jika $this->when ==now
        if ($this->when == 'now') {
            $lastDate = $today;
        }

        // jika lastDate ada
        if ($lastDate) {

            // query dlu dari history
            $initStokQuery = ProductStockHistory::where('product_id', $product->id)
                ->where($where, $id)
                ->whereDate('created_at', $lastDate)
                ->orderBy('created_at', 'asc')
                ->first();

            // query juga dari stock
            $newestStock = ProductStock::where('product_id', $product->id)
                ->where($where, $id)
                ->first();

            // action: ambil stock awal
            $initStok = $initStokQuery ? $initStokQuery->stock_before : ($newestStock ? $newestStock->stock_current : 0);

            $getFinalStokQuery = ProductStockHistory::where('product_id', $product->id)
                ->where($where, $id)
                ->whereDate('created_at', $lastDate)
                ->orderBy('created_at', 'desc')
                ->first();

            $getFinalStok = $getFinalStokQuery ? $getFinalStokQuery->stock_after : ($newestStock ? $newestStock->stock_current : 0);
        } else {
            $queryStok    = ProductStock::where($where, $id)->where('product_id', $product->id)->first();
            $initStok     = $queryStok ? $queryStok->stock_current : 0;
            $getFinalStok = $queryStok ? $queryStok->stock_current : 0;
        }

        // purchase hanya ada di inv. jadi lakukan hanya jika inventory ada
        if ($inventory_id) {
            $purchase = PurchaseDetail::whereHas('purchase', function ($q) use ($data_for_date) {
                $q->where('purchase_date', $data_for_date)
                    ->where('purchase_status', 'Finish');
            })
                ->where('product_id', $product->id)
                ->where('inventory_id', $inventory_id)
                ->get(); // Ambil semua data

            $sumQtyPurchases = $purchase->sum('qty'); // Total qty
                                                      // $sumSubtotal     = $purchase->sum('subtotal'); // Total subtotal
        } else {
            $sumQtyPurchases = 0;
            // $sumSubtotal     = 0;
        }

        // note: untuk sales. hanya ada di outlet jadi ga ada utk inventory
        if ($outlet_id) {
            $qtySales = SalesDetail::
                whereHas('sale', function ($q) use ($data_for_date) {
                $q->whereDate('sale_date', $data_for_date);
            })
                ->where('product_id', $product->id)
                ->where('outlet_id', $outlet_id)
                ->where('status', 'success')
            // ->get();
                ->sum('qty');

            Log::info($qtySales);
        } else {
            $qtySales = 0;
        }

        Log::info('qty sales : ' . $qtySales);
        // buat dapat final stok
        $initialStock = $initStok;
        // $purchases     = $sumQtyPurchases ? $sumQtyPurchases : 0;
        // $sales        = $qtySales ? $qtySales : 0;
        $sellingPrice = $product->productPrice?->where('type', 'utama')->first()?->price ?? 0;
        // $finalStock   = $initialStock + $purchases - $sales;
        $finalStock     = $getFinalStok;
        $stockValue     = $finalStock * $product->capital_price;
        $potentialValue = $finalStock * $sellingPrice;

        StockValueReport::updateOrCreate([
            'product_id'   => $product?->id,
            'report_date'  => $data_for_date,
            'outlet_id'    => $outlet_id ?? null,
            'inventory_id' => $inventory_id ?? null,
        ], [
            'product_category_id' => $product?->productCategory?->id,
            'initial_stock'       => $initialStock,
            'purchases'           => $sumQtyPurchases,
            'sales'               => $qtySales,
            'final_stock'         => $finalStock,
            'purchase_price'      => $product->capital_price,
            'stock_value'         => $stockValue,
            'selling_price'       => $sellingPrice,
            'potential_value'     => $potentialValue,
            'report_date'         => $data_for_date,
            'expired_date'        => null,
            'supplier_id'         => null,
        ]);
    }
}
