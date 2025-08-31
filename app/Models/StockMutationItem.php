<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\StockMutationItem
 *
 * @property int $id
 * @property int $stock_mutation_id
 * @property int $product_id
 * @property int $qty
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\StockMutation $stockMutation
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereStockMutationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationItem withoutTrashed()
 * @mixin \Eloquent
 */
class StockMutationItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function stockMutation()
    {
        return $this->belongsTo(StockMutation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
