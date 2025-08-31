<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PurchaseInvoice
 *
 * @property int $id
 * @property int $purchase_id
 * @property int $user_id
 * @property int|null $journal_number_id
 * @property string|null $code
 * @property string|null $invoice_number
 * @property string|null $invoice_date
 * @property string|null $nominal
 * @property int $nominal_discount
 * @property int $total_invoice
 * @property string|null $nominal_returned
 * @property string|null $nominal_paid
 * @property int $is_done
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePayment> $invoicePayments
 * @property-read int|null $invoice_payments_count
 * @property-read \App\Models\Purchase $purchase
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereInvoiceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereIsDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereNominal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereNominalDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereNominalPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereNominalReturned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereTotalInvoice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseInvoice withoutTrashed()
 * @mixin \Eloquent
 */
class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
