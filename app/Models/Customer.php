<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property int|null $customer_category_id
 * @property string|null $province_code
 * @property string|null $city_code
 * @property string|null $district_code
 * @property string|null $village_code
 * @property string $code
 * @property string $name
 * @property string $phone
 * @property string $date
 * @property string|null $sub_village
 * @property string $address
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $membership_id
 * @property-read \App\Models\CustomerCategory|null $category
 * @property-read \App\Models\Membership|null $membership
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sales> $sales
 * @property-read int|null $sales_count
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCustomerCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDistrictCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereMembershipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSubVillage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereVillageCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer withoutTrashed()
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function category()
    {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }
}
