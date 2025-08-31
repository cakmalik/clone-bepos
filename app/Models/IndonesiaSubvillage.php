<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\IndonesiaSubvillage
 *
 * @property int $id
 * @property string $village_code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage query()
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IndonesiaSubvillage whereVillageCode($value)
 * @mixin \Eloquent
 */
class IndonesiaSubvillage extends Model
{
    use HasFactory;

    protected $guarded = [];
}
