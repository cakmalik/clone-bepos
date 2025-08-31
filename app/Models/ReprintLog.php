<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\ReprintLog
 *
 * @property int $id
 * @property int $sales_id
 * @property int $user_id
 * @property string $reprint_time
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereReprintTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereSalesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReprintLog whereUserId($value)
 * @mixin \Eloquent
 */
class ReprintLog extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }
}
