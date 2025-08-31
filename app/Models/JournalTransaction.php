<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JournalTransaction
 *
 * @property int $id
 * @property string $code
 * @property int $journal_number_id
 * @property int $journal_account_id
 * @property string $type
 * @property int $nominal
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\JournalAccount $journalAccount
 * @property-read \App\Models\JournalNumber $journalNumber
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereJournalAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalTransaction withoutTrashed()
 * @mixin \Eloquent
 */
class JournalTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function journalNumber()
    {
        return $this->belongsTo(JournalNumber::class);
    }

    public function journalAccount()
    {
        return $this->belongsTo(JournalAccount::class);
    }
}
