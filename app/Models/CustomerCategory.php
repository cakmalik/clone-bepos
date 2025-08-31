<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\CustomerCategory
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $customers
 * @property-read int|null $customers_count
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerCategory withoutTrashed()
 * @mixin \Eloquent
 */
class CustomerCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_categories';
    protected $guarded = ['id'];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'customer_category_id');
    }
}
