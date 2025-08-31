<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockMutationRewardItems;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockMutationReward
 *
 * @property int $id
 * @property string $code
 * @property string $date
 * @property string $type
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockMutationRewardItems> $stock_mutation_reward_items
 * @property-read int|null $stock_mutation_reward_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationReward withoutTrashed()
 * @mixin \Eloquent
 */
class StockMutationReward extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function stock_mutation_reward_items()
    {
        return $this->hasMany(StockMutationRewardItems::class);
    }
}
