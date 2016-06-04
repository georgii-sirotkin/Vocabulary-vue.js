<?php

namespace App;

use App\ThirdPartyAuthInfo;
use App\Word;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
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
     * Add third-party auth info to user.
     *
     * @param ThirdPartyAuthInfo $ThirdPartyAuthInfo
     */
    public function addThirdPartyAuthInfo(ThirdPartyAuthInfo $ThirdPartyAuthInfo)
    {
        $this->thirdPartyAuths()->save($ThirdPartyAuthInfo);
    }

    /**
     * Add word.
     *
     * @param Word $word
     */
    public function addWord(Word $word)
    {
        $this->words()->save($word);
    }

    /**
     * Get third party authentication info that belongs to this user.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thirdPartyAuths()
    {
        return $this->hasMany(ThirdPartyAuthInfo::class);
    }

    /**
     * Get words that belong to this user.
     *
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function words()
    {
        return $this->hasMany(Word::class);
    }
}
