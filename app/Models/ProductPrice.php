<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductPrice
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $customer_category_id
 * @property int $selling_price_id
 * @property string $price
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CustomerCategory|null $customerCategory
 * @property-read \App\Models\Product|null $product
 * @property-read \App\Models\SellingPrice|null $sellingPrice
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereCustomerCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereSellingPriceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPrice withoutTrashed()
 * @mixin \Eloquent
 */
class ProductPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sellingPrice()
    {
        return $this->belongsTo(SellingPrice::class, 'selling_price_id');
    }

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }
    public function getPriceAttribute($value)
    {
        return floatval($value);
    }
}
