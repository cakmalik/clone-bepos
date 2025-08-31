<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserOutlet
 *
 * @property int $id
 * @property int $outlet_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Outlet $outlet
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereOutletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutlet withoutTrashed()
 * @mixin \Eloquent
 */
class UserOutlet extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $hidden = ["created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo(User::class)->select(['id', 'users_name', 'username']);
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class)->select(['id', 'name', 'code', 'slug', 'outlet_image']);
    }
}
