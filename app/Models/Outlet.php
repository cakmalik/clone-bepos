<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Outlet
 *
 * @property int $id
 * @property int|null $outlet_parent_id
 * @property string $type
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string|null $outlet_image
 * @property string $address
 * @property string $phone
 * @property int $is_main
 * @property string|null $desc
 * @property string|null $footer_notes
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashierMachine> $cashierMachine
 * @property-read int|null $cashier_machine_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashierMachine> $cashierMachines
 * @property-read int|null $cashier_machines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventory> $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductCategory> $productCategories
 * @property-read int|null $product_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStockHistory> $productStockHistories
 * @property-read int|null $product_stock_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $productStocks
 * @property-read int|null $product_stocks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductUnit> $productUnits
 * @property-read int|null $product_units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStock> $product_stock
 * @property-read int|null $product_stock_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Purchase> $purchase
 * @property-read int|null $purchase_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseDetail> $purchaseDetails
 * @property-read int|null $purchase_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesDetail> $salesDetails
 * @property-read int|null $sales_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpname> $stockOpnames
 * @property-read int|null $stock_opnames_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Table> $tables
 * @property-read int|null $tables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserOutlet> $userOutlets
 * @property-read int|null $user_outlets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereFooterNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereOutletImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereOutletParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Outlet withoutTrashed()
 * @mixin \Eloquent
 */
class Outlet extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }

    public function productStockHistories()
    {
        return $this->hasMany(ProductStockHistory::class);
    }

    public function cashierMachines()
    {
        return $this->hasMany(CashierMachine::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function userOutlets()
    {
        return $this->hasMany(UserOutlet::class);
    }
    public function cashierMachine()
    {
        return $this->hasMany(CashierMachine::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_outlets');
    }
    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function product_stock()
    {
        return $this->hasMany(ProductStock::class);
    }
}
