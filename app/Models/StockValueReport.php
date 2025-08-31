<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockValueReport
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $product_category_id
 * @property int|null $outlet_id
 * @property int|null $inventory_id
 * @property int $initial_stock
 * @property int $purchases
 * @property int $sales
 * @property int $final_stock
 * @property string $purchase_price
 * @property string $stock_value
 * @property string $selling_price
 * @property string $potential_value
 * @property string|null $expired_date
 * @property string $report_date
 * @property int|null $supplier_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read Product $product
 * @property-read \App\Models\ProductCategory|null $productCategory
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereExpiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereFinalStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereInitialStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport wherePotentialValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport wherePurchases($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereReportDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereSales($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereStockValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockValueReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockValueReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
