<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReceiptDetail
 *
 * @property int $id
 * @property int $purchase_detail_id
 * @property string|null $code
 * @property string|null $received_ref_code
 * @property string|null $received_date
 * @property string|null $shipment_ref_code
 * @property string|null $accepted_qty
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PurchaseDetail $purchaseDetail
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereAcceptedQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail wherePurchaseDetailId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereReceivedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereReceivedRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereShipmentRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ReceiptDetail withoutTrashed()
 * @mixin \Eloquent
 */
class ReceiptDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class);
    }
}
