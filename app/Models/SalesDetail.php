<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SalesDetail
 *
 * @property int $id
 * @property int $sales_id
 * @property int $outlet_id
 * @property int $user_id
 * @property int $product_id
 * @property int $is_bundle
 * @property int $is_item_bundle
 * @property int|null $parent_sales_detail_id
 * @property string $product_name
 * @property string $price
 * @property string|null $hpp
 * @property int $discount
 * @property string $qty
 * @property int|null $unit_id
 * @property string|null $unit_symbol
 * @property string $final_price
 * @property string $subtotal
 * @property int $profit
 * @property string $status
 * @property int $is_retur
 * @property int $qty_retur
 * @property string $process_status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $handler_user_id
 * @property int $is_tiered
 * @property string $sold_with
 * @property-read mixed $rp_final_price
 * @property-read mixed $rp_price
 * @property-read mixed $rp_profit
 * @property-read mixed $rp_subtotal
 * @property-read \App\Models\User|null $handler
 * @property-read \App\Models\Outlet $outlet
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\ProductBundle|null $productBundle
 * @property-read \App\Models\Sales $sale
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereHandlerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereHpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereIsBundle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereIsItemBundle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereIsRetur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereIsTiered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereParentSalesDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereProcessStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereQtyRetur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSoldWith($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereUnitSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesDetail withoutTrashed()
 * @mixin \Eloquent
 */
class SalesDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $hidden = ["deleted_at"];
    protected $appends = ['rp_price', 'rp_subtotal', 'rp_profit', 'rp_final_price'];

    public function sale()
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handler_user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRpPriceAttribute()
    {
        return sprintf('Rp %s', number_format($this->price));
    }

    public function getRpSubtotalAttribute()
    {
        return sprintf('Rp %s', number_format($this->subtotal));
    }

    public function getRpProfitAttribute()
    {
        return sprintf('Rp %s', number_format($this->profit));
    }

    public function getRpFinalPriceAttribute()
    {
        return sprintf('Rp %s', number_format($this->final_price));
    }
    public function getFinalPriceAttribute($value)
    {
        return floatval($value);
    }
    public function getSubtotalAttribute($value)
    {
        return floatval($value);
    }
    public function getQtyAttribute($value)
    {
        return floatval($value);
    }

    public function productBundle()
    {
        return $this->belongsTo(ProductBundle::class, 'product_bundle_id');
    }

    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class, 'product_price_id');
    }

}
