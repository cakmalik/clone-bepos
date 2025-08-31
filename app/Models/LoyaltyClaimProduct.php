<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\LoyaltyClaimProduct
 *
 * @property int $id
 * @property string $type
 * @property int $previous_score
 * @property int $qty
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct wherePreviousScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoyaltyClaimProduct extends Model
{
    use HasFactory;
    protected $table = 'loyalty_claim_products';

    protected $fillable = [
        'previous_score',
        'qty',
        'product_id',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
