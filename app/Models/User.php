<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $role_id
 * @property string|null $users_image
 * @property string $users_name
 * @property string|null $phone
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $timezone
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $pin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PurchaseInvoice> $PurchaseInvoice
 * @property-read int|null $purchase_invoice_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CashierMachine> $cashierMachines
 * @property-read int|null $cashier_machines_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Inventory> $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoicePayment> $invoicePayment
 * @property-read int|null $invoice_payment_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Outlet|null $outlet
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Outlet> $outlets
 * @property-read int|null $outlets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $product
 * @property-read int|null $product_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductStockHistory> $productStockHistories
 * @property-read int|null $product_stock_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductUnit> $productUnits
 * @property-read int|null $product_units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Purchase> $purchase
 * @property-read int|null $purchase_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Purchase> $purchases
 * @property-read int|null $purchases_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sales> $sales
 * @property-read int|null $sales_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SalesDetail> $salesDetails
 * @property-read int|null $sales_details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockOpname> $stockOpnames
 * @property-read int|null $stock_opnames_count
 * @property-read \App\Models\Supplier|null $supplier
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserOutlet> $userOutlets
 * @property-read int|null $user_outlets_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsersImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsersName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function decryptedPin()
    {
        return  $this->pin ? (int)Crypt::decryptString($this->pin) : null;
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(StockOpname::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
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
        return $this->hasMany(Sales::class);
    }

    public function salesDetails()
    {
        return $this->hasMany(SalesDetail::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function userOutlets()
    {
        return $this->hasMany(UserOutlet::class);
    }

    public function userInventories()
    {
        return $this->hasMany(UserInventory::class);
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function PurchaseInvoice()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }

    public function invoicePayment()
    {
        return $this->hasMany(InvoicePayment::class);
    }
    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'user_outlets');
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class, 'user_inventories');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isDeveloper()
    {
        return $this->role->role_name == 'DEVELOPER';
    }
}
