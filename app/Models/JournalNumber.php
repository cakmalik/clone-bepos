<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\JournalNumber
 *
 * @property int $id
 * @property int $journal_type_id
 * @property int|null $outlet_id
 * @property int|null $inventory_id
 * @property int|null $user_id
 * @property int|null $journal_closing_id
 * @property int|null $user_approved_id
 * @property string $code
 * @property string|null $ref_code
 * @property string $date
 * @property int $status_approved
 * @property int $is_done
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \App\Models\JournalClosing|null $journalClosing
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JournalTransaction> $journalTransaction
 * @property-read int|null $journal_transaction_count
 * @property-read \App\Models\JournalType $journalType
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereJournalClosingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereJournalTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereStatusApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereUserApprovedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalNumber withoutTrashed()
 * @mixin \Eloquent
 */
class JournalNumber extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function journalType()
    {
        return $this->belongsTo(JournalType::class);
    }

    public function journalClosing()
    {
        return $this->belongsTo(JournalClosing::class);
    }

    public function journalTransaction()
    {
        return $this->hasMany(JournalTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
