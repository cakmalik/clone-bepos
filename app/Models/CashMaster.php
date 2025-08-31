<?php

namespace App\Models;

use App\Models\JournalSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CashMaster
 *
 * @property int $id
 * @property int $journal_setting_id
 * @property string $code
 * @property string $name
 * @property string $cash_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read JournalSetting|null $credit_account
 * @property-read JournalSetting|null $debit_account
 * @property-read JournalSetting|null $journal_setting
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereCashType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereJournalSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashMaster withoutTrashed()
 * @mixin \Eloquent
 */
class CashMaster extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function journal_setting()
    {
        return $this->belongsTo(JournalSetting::class, 'journal_setting_id', 'id');
    }

    public function debit_account()
    {
        return $this->belongsTo(JournalSetting::class, 'debit_account_id', 'id');
    }
    public function credit_account()
    {
        return $this->belongsTo(JournalSetting::class, 'credit_account_id', 'id');
    }
}
