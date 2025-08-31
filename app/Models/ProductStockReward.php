<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductStockReward
 *
 * @property int $id
 * @property int $product_id
 * @property int $outlet_id
 * @property string $date
 * @property int $qty
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockReward withoutTrashed()
 * @mixin \Eloquent
 */
class ProductStockReward extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
}
