<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JournalType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalNumber> $journalNumbers
 * @property-read int|null $journal_numbers_count
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalType withoutTrashed()
 * @mixin \Eloquent
 */
class JournalType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function journalNumbers() {
        return $this->hasMany(JournalNumber::class);
    }
}
