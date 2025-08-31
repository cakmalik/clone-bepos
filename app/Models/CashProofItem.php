<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CashProofItem
 *
 * @property int $id
 * @property int $cash_proof_id
 * @property int $cash_master_id
 * @property string $ref_code
 * @property string|null $description
 * @property int $nominal
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CashMaster $cashMaster
 * @property-read \App\Models\CashProof $cashProof
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereCashMasterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereCashProofId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CashProofItem withoutTrashed()
 * @mixin \Eloquent
 */
class CashProofItem extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function cashProof()
    {
        return $this->belongsTo(CashProof::class);
    }

    public function cashMaster()
    {
        return $this->belongsTo(CashMaster::class);
    }
}
