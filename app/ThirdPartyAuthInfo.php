<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThirdPartyAuthInfo extends Model
{
    public $timestamps = false;
    protected $table = 'third_party_auth';

    /**
     * Get the user who owns this auth info.
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
