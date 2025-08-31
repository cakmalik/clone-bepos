<?php

namespace App\Models;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\StockMutationReward;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockMutationRewardItems
 *
 * @property int $id
 * @property int $stock_mutation_reward_id
 * @property int $product_id
 * @property int $outlet_id
 * @property string $date
 * @property int $qty
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Outlet|null $outlet
 * @property-read Product|null $product
 * @property-read StockMutationReward|null $stock_mutation_reward
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereStockMutationRewardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationRewardItems withoutTrashed()
 * @mixin \Eloquent
 */
class StockMutationRewardItems extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function stock_mutation_reward()
    {
        return $this->belongsTo(StockMutationReward::class);
    }
}
