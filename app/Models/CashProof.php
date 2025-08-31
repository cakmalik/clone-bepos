<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CashProof
 *
 * @property int $id
 * @property string $code
 * @property string $date
 * @property string $received_from
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashProofItem> $items
 * @property-read int|null $items_count
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereReceivedFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProof withoutTrashed()
 * @mixin \Eloquent
 */
class CashProof extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(CashProofItem::class);
    }
}
