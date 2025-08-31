<?php

namespace App\Models;

use App\Models\JournalSetting;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\JournalAccount;

/**
 * App\Models\JournalCategoryProduct
 *
 * @property int $id
 * @property int $product_category_id
 * @property int $journal_setting_trans_id
 * @property int $journal_setting_buy_id
 * @property int $journal_setting_invoice_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read ProductCategory|null $category
 * @property-read JournalAccount|null $credit_account
 * @property-read JournalAccount|null $debit_account
 * @property-read JournalSetting|null $journal_settings_buy
 * @property-read JournalSetting|null $journal_settings_invoice
 * @property-read JournalSetting|null $journal_settings_trans
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereJournalSettingBuyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereJournalSettingInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereJournalSettingTransId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|JournalCategoryProduct withoutTrashed()
 * @mixin \Eloquent
 */
class JournalCategoryProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];


    public function journal_settings_trans()
    {
        return $this->belongsTo(JournalSetting::class, 'journal_setting_trans_id', 'id');
    }
    public function journal_settings_buy()
    {
        return $this->belongsTo(JournalSetting::class, 'journal_setting_buy_id', 'id');
    }
    public function journal_settings_invoice()
    {
        return $this->belongsTo(JournalSetting::class, 'journal_setting_invoice_id', 'id');
    }


    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id', 'id');
    }

    public function debit_account()
    {
        return $this->belongsTo(JournalAccount::class, 'debit_account_id', 'id');
    }
    public function credit_account()
    {
        return $this->belongsTo(JournalAccount::class, 'credit_account_id', 'id');
    }
}
