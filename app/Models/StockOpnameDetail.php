<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockOpnameDetail
 *
 * @property int $id
 * @property int $stock_opname_id
 * @property int $product_id
 * @property string $code
 * @property string|null $ref_code
 * @property int|null $qty_system
 * @property int|null $qty_so
 * @property int|null $qty_selisih
 * @property int|null $qty_adjustment
 * @property int|null $qty_after_adjustment
 * @property int|null $adjustment_nominal_value
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Product $product
 * @property-read \App\Models\ProductCategory|null $product_category
 * @property-read \App\Models\ProductUnit|null $product_unit
 * @property-read \App\Models\StockOpname|null $refCode
 * @property-read \App\Models\StockOpname $stockOpname
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereAdjustmentNominalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereQtyAdjustment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereQtyAfterAdjustment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereQtySelisih($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereQtySo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereQtySystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereStockOpnameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpnameDetail withoutTrashed()
 * @mixin \Eloquent
 */
class StockOpnameDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function product_unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id')->withTrashed();  
    }

    public function refCode()
    {
        return $this->belongsTo(StockOpname::class, 'ref_code');
    }
}