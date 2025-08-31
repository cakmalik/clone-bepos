<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\StockMutationInventoryOutlet
 *
 * @property int $id
 * @property string $code
 * @property string $date
 * @property int $inventory_source_id
 * @property int $outlet_destination_id
 * @property string $status
 * @property int|null $approved_user_id
 * @property int|null $received_user_id
 * @property int|null $creator_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet $Destination
 * @property-read \App\Models\Outlet $OutletDestination
 * @property-read \App\Models\Inventory $Source
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Inventory $inventorySource
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMutationInventoryOutletItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $receivedBy
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereApprovedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereCreatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereInventorySourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereOutletDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereReceivedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutationInventoryOutlet whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockMutationInventoryOutlet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function inventorySource()
    {
        return $this->belongsTo(Inventory::class, 'inventory_source_id', 'id');
    }

    public function Source()
    {
        return $this->belongsTo(Inventory::class, 'inventory_source_id', 'id');
    }

    public function OutletDestination()
    {
        return $this->belongsTo(Outlet::class, 'outlet_destination_id', 'id');
    }
    public function Destination()
    {
        return $this->belongsTo(Outlet::class, 'outlet_destination_id', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_user_id', 'id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_user_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(StockMutationInventoryOutletItem::class, 'stock_mutation_id', 'id');
    }
}
