<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LoyaltyPlusQty
 *
 * @property int $id
 * @property string $min_transaction
 * @property int $point_plus
 * @property int $applies_multiply
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty whereAppliesMultiply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty whereMinTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty wherePointPlus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPlusQty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoyaltyPlusQty extends Model
{
    use HasFactory;

    protected $table = 'loyalty_plus_qty';

    protected $fillable = [
        'min_transaction',
        'point_plus',
        'applies_multiply',
    ];
}
