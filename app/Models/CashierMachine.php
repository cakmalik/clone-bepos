<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CashierMachine
 *
 * @property int $id
 * @property int $outlet_id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet $outlet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sales> $sales
 * @property-read int|null $sales_count
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashierMachine withoutTrashed()
 * @mixin \Eloquent
 */
class CashierMachine extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
}
