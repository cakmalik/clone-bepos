<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InvoicePayment
 *
 * @property int $id
 * @property int $purchase_invoice_id
 * @property int $user_id
 * @property int|null $journal_number_id
 * @property string|null $code
 * @property int|null $nominal_payment
 * @property string|null $payment_date
 * @property string|null $payment_type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PurchaseInvoice $purchaseInvoice
 * @property-read \App\Models\PurchaseInvoice $purchase_invoice_new
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereNominalPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment wherePurchaseInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoicePayment withoutTrashed()
 * @mixin \Eloquent
 */
class InvoicePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase_invoice_new()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id', 'id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }
}
