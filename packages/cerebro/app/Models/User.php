<?php

namespace App\Models;

use App\Casts\Google2fa;
use App\Jobs\QueuedVerifyEmailJob;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\UserResolver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements UserResolver, MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;
    use SoftDeletes;
    use HasRoles;

    const TYPE_SOURCE_PERSONAL = 'personal';
    const TYPE_SOURCE_LEAD = 'payday';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'email_ccpa',
        'password',
        'min_price',
        'lead_min_price',
        'processing_time',
        'redirects_count',
        'post_back_amount',
        'personal_min_req',
        'personal_channel_id',
        'personal_password',
        'lead_channel_id',
        'lead_password',
        'menuroles',
        'post_back_url',
        'google2fa_secret',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'menuroles',
        'roles',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
        'google2fa_secret' => Google2fa::class
    ];

    protected $dates = [
        'deleted_at'
    ];

    protected $appends = [
        'role'
    ];

    public function getRoleAttribute()
    {
        if ($this->hasRole('admin')) {
            return 'admin';
        } else {
            return 'user';
        }
    }

    public static function resolve()
    {
        return Auth::check() ? Auth::user()->getAuthIdentifier() : null;
    }

    /**
     * Get the notes for the users.
     */
    public function notes()
    {
        return $this->hasMany('App\Models\Notes');
    }

    public function sendEmailVerificationNotification()
    {
        QueuedVerifyEmailJob::dispatchAfterResponse($this);
    }

    static function regenerateToken(User $user)
    {
        $user->tokens()->delete();
        return $user->createToken('cerebroclienttoken')->plainTextToken;
    }
}
