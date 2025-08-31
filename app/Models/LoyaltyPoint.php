<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LoyaltyPoint
 *
 * @property int $id
 * @property string $type
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoyaltyPoint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoyaltyPoint extends Model
{
    protected $table = 'loyalty_settings';
    protected $fillable = ['is_active'];
    use HasFactory;
}
