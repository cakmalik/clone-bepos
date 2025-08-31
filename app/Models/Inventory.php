<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Inventory
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $code
 * @property string $name
 * @property string $type
 * @property int $is_parent
 * @property int $is_active
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read Inventory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $product_stock
 * @property-read int|null $product_stock_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory query()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereIsParent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Inventory withoutTrashed()
 * @mixin \Eloquent
 */
class Inventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(Inventory::class, 'parent_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_inventories', 'inventory_id', 'id');
    }

    public function product_stock()
    {
        return $this->hasMany(ProductStock::class);
    }
}
