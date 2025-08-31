<?php

namespace App\Models;

use App\Models\CashMaster;
use App\Models\JournalAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\JournalSetting
 *
 * @property int $id
 * @property int|null $debit_account_id
 * @property int|null $credit_account_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CashMaster> $cash_master
 * @property-read int|null $cash_master_count
 * @property-read JournalAccount|null $credit_account
 * @property-read JournalAccount|null $debit_account
 * @property-read JournalAccount|null $journal_account
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereCreditAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereDebitAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalSetting withoutTrashed()
 * @mixin \Eloquent
 */
class JournalSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function cash_master()
    {
        return $this->hasMany(CashMaster::class);
    }

    public function journal_account()
    {
        return $this->belongsTo(JournalAccount::class);
    }

    public function debit_account()
    {
        return $this->belongsTo(JournalAccount::class, 'debit_account_id', 'id');
    }
    public function credit_account()
    {
        return $this->belongsTo(JournalAccount::class, 'credit_account_id', 'id');
    }
}
