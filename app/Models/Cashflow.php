<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Cashflow
 *
 * @property int $id
 * @property int $outlet_id
 * @property int $user_id
 * @property int|null $cashflow_close_id
 * @property string $code
 * @property string|null $transaction_code
 * @property string|null $transaction_date
 * @property-write string $type
 * @property string $amount
 * @property string $total_hpp
 * @property string|null $profit
 * @property string $desc
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CashflowClose|null $cashflowClose
 * @property-read \App\Models\Sales|null $transaction
 * @method static \Database\Factories\CashflowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow outlet($outlet_id)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereCashflowCloseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereProfit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereTotalHpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereTransactionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Cashflow withoutTrashed()
 * @mixin \Eloquent
 */
class Cashflow extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    // protected $attributes = [
    //     'code' => false
    // ];
    public function cashflowClose()
    {
        return $this->hasOne(CashflowClose::class);
    }
    public function type(): Attribute
    {
        return new Attribute(
            set: fn($value) => $value ?? 'in',
        );
    }
    public function scopeOutlet($query, $outlet_id)
    {
        return $query->where('outlet_id', $outlet_id)->whereNull('cashflow_close_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Sales::class, 'transaction_code', 'sale_code');
    }

    public function getAmountAttribute($value)
    {
        return floatval($value);
    }

    public function getTotalHppAttribute($value)
    {
        return floatval($value);
    }

    public function getProfitAttribute($value)
    {
        return floatval($value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
