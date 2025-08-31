<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ProductDiscount
 *
 * @property int $id
 * @property int $product_id
 * @property int $amount
 * @property string $discount_type
 * @property string $start_date
 * @property string $expired_date
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereExpiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductDiscount withoutTrashed()
 * @mixin \Eloquent
 */
class ProductDiscount extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
