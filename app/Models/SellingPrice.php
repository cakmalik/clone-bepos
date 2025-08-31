<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SellingPrice
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductPrice> $productPrices
 * @property-read int|null $product_prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SellingPrice withoutTrashed()
 * @mixin \Eloquent
 */
class SellingPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function productPrices()
    {
        return $this->hasMany(ProductPrice::class, 'selling_price_id', 'id');
    }
}
