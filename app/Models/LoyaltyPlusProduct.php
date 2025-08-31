<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\LoyaltyPlusProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $membership_id
 * @property int $point_plus
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Membership $membership
 * @property-read Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct whereMembershipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct wherePointPlus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoyaltyPlusProduct extends Model
{
    protected $table = 'loyalty_plus_product';

    protected $fillable = [
        'product_id',
        'membership_id',
        'point_plus',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
