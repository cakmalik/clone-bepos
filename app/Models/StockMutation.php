<?php

namespace App\Models;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockMutation
 *
 * @property int $id
 * @property string $code
 * @property string $date
 * @property int|null $inventory_source_id
 * @property int|null $inventory_destination_id
 * @property int|null $outlet_source_id
 * @property int|null $outlet_destination_id
 * @property string $status
 * @property string|null $mutation_category
 * @property int|null $approved_user_id
 * @property int|null $received_user_id
 * @property int|null $creator_user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory|null $Destination
 * @property-read Outlet|null $OutletDestination
 * @property-read Outlet|null $OutletSource
 * @property-read \App\Models\Inventory|null $Source
 * @property-read \App\Models\User|null $approvedBy
 * @property-read \App\Models\User|null $creator
 * @property-read \App\Models\Inventory|null $inventoryDestination
 * @property-read \App\Models\Inventory|null $inventorySource
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockMutationItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User|null $receivedBy
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereApprovedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereCreatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereInventoryDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereInventorySourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereMutationCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereOutletDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereOutletSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereReceivedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockMutation withoutTrashed()
 * @mixin \Eloquent
 */
class StockMutation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function inventorySource()
    {
        return $this->belongsTo(Inventory::class, 'inventory_source_id', 'id');
    }

    public function inventoryDestination()
    {
        return $this->belongsTo(Inventory::class, 'inventory_destination_id', 'id');
    }
    
    public function outletSource()
    {
        return $this->belongsTo(Outlet::class, 'outlet_source_id', 'id');
    }
    
    public function outletDestination()
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
        return $this->hasMany(StockMutationItem::class);
    }

    // ✅ Accessor untuk source_name
    public function getSourceNameAttribute()
    {
        if ($this->inventory_source_id) {
            return $this->inventorySource->name ?? '-';
        }
        if ($this->outlet_source_id) {
            return $this->outletSource->name ?? '-';
        }
        return '-';
    }

    // ✅ Accessor untuk destination_name
    public function getDestinationNameAttribute()
    {
        if ($this->inventory_destination_id) {
            return $this->inventoryDestination->name ?? '-';
        }
        if ($this->outlet_destination_id) {
            return $this->outletDestination->name ?? '-';
        }
        return '-';
    }
}