<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserInventory
 *
 * @property int $id
 * @property int $user_id
 * @property int $inventory_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserInventory whereUserId($value)
 * @mixin \Eloquent
 */
class UserInventory extends Model
{
    use HasFactory;

    protected $guarded = [];
}
