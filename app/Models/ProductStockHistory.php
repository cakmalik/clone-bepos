<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductStockHistory
 *
 * @property int $id
 * @property int|null $outlet_id
 * @property int $user_id
 * @property int $product_id
 * @property int|null $inventory_id
 * @property string|null $document_number
 * @property string|null $history_date
 * @property string $stock_change
 * @property string $stock_before
 * @property string $stock_after
 * @property string $action_type
 * @property string $desc
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereDocumentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereHistoryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereStockAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereStockBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereStockChange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductStockHistory withoutTrashed()
 * @mixin \Eloquent
 */
class ProductStockHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStockChangeAttribute($value)
    {
        return floatval($value);
    }

    public function getStockAfterAttribute($value)
    {
        return floatval($value);
    }

    public function getStockBeforeAttribute($value)
    {
        return floatval($value);
    }
}
