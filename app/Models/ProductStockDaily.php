<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProductStockDaily
 *
 * @property int $id
 * @property int $product_id
 * @property int $inventory_id
 * @property int $outlet_id
 * @property int $qty
 * @property string $recap_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereRecapDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockDaily whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductStockDaily extends Model
{
    use HasFactory;

    protected $guarded = [];
}
