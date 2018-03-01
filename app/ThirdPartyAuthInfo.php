<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ThirdPartyAuthInfo
 *
 * @property int $id
 * @property string $third_party
 * @property string $third_party_user_id
 * @property int $user_id
 * @property-read \App\User $user
 * @mixin \Eloquent
 */
class ThirdPartyAuthInfo extends Model
{
    public $timestamps = false;
    protected $table = 'third_party_auth';
    protected $fillable = [
        'third_party',
        'third_party_user_id',
    ];

    /**
     * Get the user who owns this auth info.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
