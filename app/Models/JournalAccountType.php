<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JournalAccountType
 *
 * @property int $id
 * @property string $name
 * @property string $transaction_type
 * @property string $position
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalAccount> $journalAccounts
 * @property-read int|null $journal_accounts_count
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalAccountType withoutTrashed()
 * @mixin \Eloquent
 */
class JournalAccountType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function journalAccounts()
    {
        return $this->hasMany(JournalAccount::class);
    }
}
