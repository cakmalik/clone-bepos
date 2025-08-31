<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LoyaltyClaimDiscount
 *
 * @property int $id
 * @property int $previous_score
 * @property int $value
 * @property string $type
 * @property string $discount_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\LoyaltyClaimProduct|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount wherePreviousScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyClaimDiscount whereValue($value)
 * @mixin \Eloquent
 */
class LoyaltyClaimDiscount extends Model
{
    use HasFactory;
    protected $table = 'loyalty_claim_discounts';

    protected $fillable = [
        'previous_score',
        'value',
        'type',
    ];
    public function product()
    {
        return $this->belongsTo(LoyaltyClaimProduct::class, 'product_id');
    }
    public function getType()
{
    return 'CLAIM DISCOUNT';
}

public function getDetails()
{
    return "DISCOUNT: {$this->value}%";
}
}
