<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Purchase
 *
 * @property int $id
 * @property int|null $supplier_id
 * @property int $inventory_id
 * @property int|null $purchase_invoice_id
 * @property int|null $journal_number_id
 * @property int $user_id
 * @property string $code
 * @property string|null $ref_code
 * @property string|null $name
 * @property string $purchase_date
 * @property string|null $nominal_amount
 * @property string|null $discount
 * @property string|null $final_amount
 * @property string $purchase_status
 * @property string $purchase_type
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Inventory|null $inventory
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchaseDetails
 * @property-read int|null $purchase_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchaseDetailsNota
 * @property-read int|null $purchase_details_nota_count
 * @property-read \App\Models\PurchaseInvoice|null $purchaseInvoice
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseInvoice> $purchaseInvoices
 * @property-read int|null $purchase_invoices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchase_detail_po
 * @property-read int|null $purchase_detail_po_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchase_detail_reception
 * @property-read int|null $purchase_detail_reception_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchase_detail_retur
 * @property-read int|null $purchase_detail_retur_count
 * @property-read \App\Models\PurchaseInvoice|null $purchase_invoice_retur
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereFinalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereNominalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePurchaseType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase withoutTrashed()
 * @mixin \Eloquent
 */
class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_po_id', 'id');
    }

    public function purchaseDetailsNota()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function purchase_detail_po()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_po_id');
    }

    public function purchase_detail_reception()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_receipt_id');
    }

    public function purchase_detail_retur()
    {
        return $this->hasMany(PurchaseDetail::class, 'code', 'code');
    }

    public function purchase_invoice_retur()
    {
        return $this->belongsTo(PurchaseInvoice::class,  'purchase_invoice_id', 'id');
    }



}
