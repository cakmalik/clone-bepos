<?php

namespace App\Models;

use App\Models\JournalSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\JournalAccount
 *
 * @property int $id
 * @property int $journal_account_type_id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JournalAccountType $journalAccountType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalTransaction> $journalTransactions
 * @property-read int|null $journal_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JournalSetting> $journal_settings
 * @property-read int|null $journal_settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereJournalAccountTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccount withoutTrashed()
 * @mixin \Eloquent
 */
class JournalAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function journalAccountType()
    {
        return $this->belongsTo(JournalAccountType::class);
    }

    public function journalTransactions()
    {
        return $this->hasMany(JournalTransaction::class);
    }

    public function journal_settings()
    {
        return $this->hasMany(JournalSetting::class);
    }
}
