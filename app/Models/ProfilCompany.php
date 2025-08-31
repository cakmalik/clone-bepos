<?php

namespace App\Models;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ProfilCompany
 *
 * @property int $id
 * @property string|null $image
 * @property string|null $name
 * @property string|null $email
 * @property string|null $address
 * @property string|null $about
 * @property string|null $telp
 * @property string $status
 * @property string|null $start_time
 * @property int|null $trial_duration
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $product_version
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereProductVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereTelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereTrialDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProfilCompany withoutTrashed()
 * @mixin \Eloquent
 */
class ProfilCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public static function getCompany(): ?self
    {
        return self::first();
    }

    public static function checkConfigByProductVersion(string $key)
    {
        return self::getCompany()?->product_version === 'retail_pro'
            ? config("version_pro.$key")
            : config("version_advance.$key");
    }

    public static function canCreateOutlet(): bool
    {
        $outletMax = self::checkConfigByProductVersion('outlet_max');

        return $outletMax && Outlet::count() < $outletMax;
    }

    public static function canManageSellingPrice(): bool
    {
        $sellingPrice = self::checkConfigByProductVersion('selling_price');

        return $sellingPrice > 0;
    }

    public static function canAddProduct(): bool
    {
        $itemSkuMax = self::checkConfigByProductVersion('item_sku_max');

        return $itemSkuMax && Product::count() < $itemSkuMax;
    }
}
