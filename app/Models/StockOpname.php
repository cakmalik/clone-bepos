<?php

namespace App\Models;

use App\Models\StockOpnameDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\StockOpname
 *
 * @property int $id
 * @property int|null $inventory_id
 * @property int|null $outlet_id
 * @property int $user_id
 * @property int|null $journal_number_id
 * @property string $code
 * @property string|null $ref_code
 * @property string $so_date
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockOpnameDetail> $adjustment_detail
 * @property-read int|null $adjustment_detail_count
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read StockOpname|null $refCode
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StockOpnameDetail> $stockOpnameDetails
 * @property-read int|null $stock_opname_details_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname query()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereSoDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StockOpname withoutTrashed()
 * @mixin \Eloquent
 */
class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockOpnameDetails()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function refCode()
    {
        return $this->belongsTo(StockOpname::class, 'ref_code');
    }

    public function adjustment_detail()
    {
        return $this->hasMany(StockOpnameDetail::class, 'ref_code', 'code');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}