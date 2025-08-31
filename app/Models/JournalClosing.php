<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JournalClosing
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $date
 * @property int $is_done
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalNumber> $journalNumbers
 * @property-read int|null $journal_numbers_count
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalClosing withoutTrashed()
 * @mixin \Eloquent
 */
class JournalClosing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'date',
    ];

    public function journalNumbers() {
        return $this->hasMany(JournalNumber::class);
    }
}
