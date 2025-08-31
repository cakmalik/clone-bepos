<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\ReprintLog;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Sales
 *
 * @property int $id
 * @property int $outlet_id
 * @property int $cashier_machine_id
 * @property int $user_id
 * @property int $payment_method_id
 * @property int|null $journal_number_id
 * @property string $sale_code
 * @property string|null $ref_code
 * @property int|null $customer_id
 * @property string $sale_date
 * @property int $nominal_amount
 * @property int $discount_amount
 * @property string $discount_type
 * @property int $transaction_fees
 * @property int $final_amount
 * @property int $nominal_pay
 * @property int $nominal_refund
 * @property string|null $refund_reason
 * @property int $nominal_change
 * @property int $is_retur
 * @property string $status
 * @property string $process_status
 * @property string $payment_status
 * @property string $shipping_status
 * @property int|null $bank_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $sales_parent_id
 * @property string $sales_type
 * @property int|null $table_id
 * @property int $creator_user_id
 * @property int|null $cashier_user_id
 * @property string|null $receipt_code
 * @property string|null $due_date
 * @property int $reprint_count
 * @property-read Bank|null $bank
 * @property-read \App\Models\User|null $cashier
 * @property-read \App\Models\CashierMachine $cashierMachine
 * @property-read \App\Models\User $creator
 * @property-read \App\Models\Customer|null $customer
 * @property-read mixed $formatted_sale_date
 * @property-read mixed $human_time
 * @property-read mixed $payment_method_name
 * @property-read \App\Models\Outlet $outlet
 * @property-read PaymentMethod $paymentMethod
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ReprintLog> $reprintLogs
 * @property-read int|null $reprint_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesDetail> $salesDetails
 * @property-read int|null $sales_details_count
 * @property-read \App\Models\Table|null $table
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Sales newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereCashierMachineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereCashierUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereCreatorUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereDiscountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereFinalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereIsRetur($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereJournalNumberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereNominalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereNominalChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereNominalPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereNominalRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereProcessStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereReceiptCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereRefCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereRefundReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereReprintCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereSaleCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereSaleDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereSalesParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereSalesType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereShippingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereTransactionFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sales withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sales withoutTrashed()
 * @mixin \Eloquent
 */
class Sales extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $appends = ['human_time'];
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function cashierMachine()
    {
        return $this->belongsTo(CashierMachine::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withDefault([
            'name' => 'Walk-in-customer',
            'code' => '-',
            'phone' => '-',
            'address' => '-',
        ]);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->translatedFormat('d/m/Y H:i')
        );
    }
    // public function nominalAmount(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($val) => sprintf('Rp %s', number_format($val))
    //     );
    // }
    public function getHumanTimeAttribute()
    {
        return Carbon::parse($this->sale_date)->diffForHumans();
    }

    public function getFormattedSaleDateAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['sale_date'])->format('d-m-Y H:i');
    }

    public function getPaymentMethodNameAttribute()
    {
        return $this->paymentMethod->name;
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function reprintLogs()
    {
        return $this->hasMany(ReprintLog::class);
    }


}
