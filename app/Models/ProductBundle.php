<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductBundle
 *
 * @property int $id
 * @property int $product_bundle_id
 * @property int|null $product_id
 * @property int $qty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $bundledProduct
 * @property-read \App\Models\Product|null $mainProduct
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesDetail> $salesDetails
 * @property-read int|null $sales_details_count
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereProductBundleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductBundle whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductBundle extends Model
{
    use HasFactory;

    protected $table = 'product_bundle_items';

    protected $guarded = [];

    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bundledProduct()
    {
        return $this->belongsTo(Product::class, 'product_bundle_id');
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class, 'product_bundle_id');
    }
}
