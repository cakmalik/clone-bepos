<?php

namespace App\Models;

use App\Models\User;
use App\Models\Sales;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SalesPayment
 *
 * @property int $id
 * @property int $sale_id
 * @property int $payment_method_id
 * @property int $user_id
 * @property string $code
 * @property int $nominal_payment
 * @property string $payment_date
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read PaymentMethod $paymentMethod
 * @property-read Sales $sale
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereNominalPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SalesPayment whereUserId($value)
 * @mixin \Eloquent
 */
class SalesPayment extends Model
{
    use HasFactory;

    protected $table = 'sales_payments';

    protected $fillable = [
        'sale_id',
        'payment_method_id',
        'user_id',
        'code',
        'nominal_payment',
        'payment_date',
        'description',
    ];

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
