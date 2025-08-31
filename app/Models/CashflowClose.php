<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CashflowClose
 *
 * @property int $id
 * @property int $outlet_id
 * @property int $user_id
 * @property string $capital_amount
 * @property string $income_amount
 * @property string $expense_amount
 * @property string $date
 * @property string $profit_amount
 * @property string $difference
 * @property string $real_amount
 * @property string $close_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cashflow|null $cashflow
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cashflow> $cashflows
 * @property-read int|null $cashflows_count
 * @property-read mixed $capiptal_amount
 * @property-read \App\Models\Outlet $outlet
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CashflowCloseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereCapitalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereCloseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereExpenseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereProfitAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereRealAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashflowClose withoutTrashed()
 * @mixin \Eloquent
 */
class CashflowClose extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'cashflow_close';
    protected $guarded = [];

    public function cashflow()
    {
        return $this->belongsTo(Cashflow::class);
    }

    public function cashflows()
    {
        return $this->hasMany(Cashflow::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getCapiptalAmountAttribute($value)
    {
        return floatval($value);
    }

    public function getIncomeAmountAttribute($value)
    {
        return floatval($value);
    }

    public function getExpenseAmountAttribute($value)
    {
        return floatval($value);
    }

    public function getProfitAmountAttribute($value)
    {
        return floatval($value);
    }

    public function getDifferenceAttribute($value)
    {
        return floatval($value);
    }

    public function getRealAmountAttribute($value)
    {
        return floatval($value);
    }

}
