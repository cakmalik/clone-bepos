<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\PurchaseDetail
 *
 * @property int $id
 * @property int|null $inventory_id
 * @property int $purchase_id
 * @property int $product_id
 * @property int|null $purchase_po_id
 * @property int|null $purchase_receipt_id
 * @property string|null $code
 * @property string|null $product_code
 * @property string|null $product_name
 * @property string|null $qty
 * @property string|null $price
 * @property string|null $discount
 * @property string|null $final_price
 * @property string|null $subtotal
 * @property string|null $accepted_qty
 * @property string|null $returned_qty
 * @property string|null $status
 * @property int $is_bonus
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \App\Models\Product $product
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductPrice> $productPrices
 * @property-read int|null $product_prices_count
 * @property-read \App\Models\Purchase $purchase
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ReceiptDetail> $receiptDetails
 * @property-read int|null $receipt_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereAcceptedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereIsBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail wherePurchasePoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail wherePurchaseReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereReturnedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseDetail withoutTrashed()
 * @mixin \Eloquent
 */
class PurchaseDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function receiptDetails()
    {
        return $this->hasMany(ReceiptDetail::class);
    }
    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function getPriceAttribute($value)
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
}
