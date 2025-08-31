<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\ProductPrice;
use App\Models\TieredPrices;
use App\Models\ProductDiscount;
use App\Models\ProductSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string|null $barcode
 * @property int $outlet_id
 * @property int $user_id
 * @property int $product_category_id
 * @property int $product_unit_id
 * @property int|null $brand_id
 * @property string $code
 * @property string $name
 * @property string|null $slug
 * @property string|null $type_product
 * @property string|null $minimum_stock
 * @property string|null $capital_price
 * @property string|null $product_image
 * @property string|null $desc
 * @property string|null $image
 * @property int $is_bundle
 * @property int $is_main_stock
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $unit_id
 * @property int $is_support_qty_decimal
 * @property-read Brand|null $brand
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductBundle> $bundledWith
 * @property-read int|null $bundled_with_count
 * @property-read mixed $price
 * @property-read \App\Models\Outlet $outlet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductPrice> $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductBundle> $productBundles
 * @property-read int|null $product_bundles_count
 * @property-read \App\Models\ProductCategory $productCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductPrice> $productPrice
 * @property-read int|null $product_price_count
 * @property-read ProductPrice|null $productPriceUtama
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $productStock
 * @property-read int|null $product_stock_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStockHistory> $productStockHistories
 * @property-read int|null $product_stock_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductSupplier> $productSupplier
 * @property-read int|null $product_supplier_count
 * @property-read \App\Models\ProductUnit $productUnit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductDiscount> $product_discount
 * @property-read int|null $product_discount_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesDetail> $salesDetails
 * @property-read int|null $sales_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpnameDetail> $stockOpnameDetails
 * @property-read int|null $stock_opname_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TieredPrices> $tieres
 * @property-read int|null $tieres_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCapitalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsBundle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsMainStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereIsSupportQtyDecimal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinimumStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereProductUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereTypeProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;



    protected $guarded = [];
    // protected $with = ['productStock', 'productPrice'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            // Menghapus semua ProdukDiscount terkait
            $product->product_discount()->delete();
        });
    }



    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id', 'id');
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productPrice()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function productStock()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stockOpnameDetails()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function productStockHistories()
    {
        return $this->hasMany(ProductStockHistory::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function product_discount()
    {
        return $this->hasMany(ProductDiscount::class);
    }

    public function productPriceUtama()
    {
        return $this->hasOne(ProductPrice::class)->where('type', 'utama');
    }


    public function productSupplier()
    {
        return $this->hasMany(ProductSupplier::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function tieres()
    {

        return $this->hasMany(TieredPrices::class);
    }

    public function productBundles()
    {
        return $this->hasMany(ProductBundle::class, 'product_bundle_id');
    }

    public function bundledWith()
    {
        return $this->hasMany(ProductBundle::class, 'product_id');
    }

    public function getCapitalPriceAttribute($value)
    {
        return floatval($value);
    }

    public function getMinimumStockAttribute($value)
    {
        return floatval($value);
    }

    public function getPriceAttribute($value)
    {
        return floatval($value);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers', 'product_id', 'supplier_id');
    }

}
