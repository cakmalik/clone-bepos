<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StockMutationInventoryOutletItem
 *
 * @property int $id
 * @property int $stock_mutation_id
 * @property int $product_id
 * @property int $qty
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\StockMutationInventoryOutlet|null $StockMutationInventoryOutlet
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereStockMutationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutletItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockMutationInventoryOutletItem extends Model
{
    use HasFactory;


    protected $guarded = ['id'];

    public function StockMutationInventoryOutlet()
    {
        return $this->belongsTo(StockMutationInventoryOutlet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
