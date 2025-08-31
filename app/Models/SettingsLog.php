<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SettingsLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $model
 * @property string $status
 * @property mixed|null $old_data
 * @property mixed $current_data
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereCurrentData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereOldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsLog whereUserId($value)
 * @mixin \Eloquent
 */
class SettingsLog extends Model
{
    use HasFactory;
    protected $guarded = [];
}
