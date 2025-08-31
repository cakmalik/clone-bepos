<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Membership
 *
 * @property int $id
 * @property string $name
 * @property int $score_min
 * @property int $score_max
 * @property string $score_loyalty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership query()
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereScoreLoyalty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereScoreMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereScoreMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Membership whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Membership extends Model
{
    protected $fillable = [
        'name',
        'score_min',
        'score_max',
        'score_loyalty',
    ];
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
