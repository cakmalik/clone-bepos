<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PriceChange
 *
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property string $product_name
 * @property string $date
 * @property int $hpp
 * @property int $selling_price
 * @property int $hpp_old
 * @property int $selling_price_old
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereHpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereHppOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereSellingPriceOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChange whereUserId($value)
 * @mixin \Eloquent
 */
class PriceChange extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
