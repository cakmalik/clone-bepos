<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PaymentMethod
 *
 * @property int $id
 * @property string $name
 * @property int $transaction_fees
 * @property int $is_active
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Sales|null $sale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sales> $sales
 * @property-read int|null $sales_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereTransactionFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethod withoutTrashed()
 * @mixin \Eloquent
 */
class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
    public function sale()
    {
        return $this->hasOne(Sales::class);
    }
}
