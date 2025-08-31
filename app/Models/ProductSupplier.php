<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ProductSupplier
 *
 * @property int $id
 * @property int $product_id
 * @property int $supplier_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Product $product
 * @property-read Supplier $supplier
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSupplier withoutTrashed()
 * @mixin \Eloquent
 */
class ProductSupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
