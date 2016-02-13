<?php

namespace App;

use App\ThirdPartyAuthInfo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get third party authentication info that belongs to this user.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thirdPartyAuths()
    {
        return $this->hasMany(ThirdPartyAuthInfo::class);
    }
}
