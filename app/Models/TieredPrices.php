<?php

namespace App\Models;

use App\Models\Product;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\TieredPrices
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $outlet_id
 * @property int $min_qty
 * @property int $max_qty
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices query()
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereMaxQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereMinQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TieredPrices withoutTrashed()
 * @mixin \Eloquent
 */
class TieredPrices extends Model
{
    use LogsActivity;
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn(string $eventName) => "Data di {$eventName}")
        ->useLogName('system-tiered-prices');
    }

    public function outlet(){
        return $this->belongsTo(Outlet::class);
    }
}
