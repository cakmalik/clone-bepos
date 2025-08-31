<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductUnit
 *
 * @property int $id
 * @property int|null $base_unit_id
 * @property string|null $symbol
 * @property string|null $conversion_rate
 * @property int $outlet_id
 * @property int $user_id
 * @property string $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereBaseUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereConversionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductUnit withoutTrashed()
 * @mixin \Eloquent
 */
class ProductUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
