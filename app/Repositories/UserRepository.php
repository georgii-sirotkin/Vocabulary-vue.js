<?php

namespace App\Repositories;

use App\ThirdPartyAuthInfo;
use App\User;

class UserRepository
{

    /**
     * Retrieve ThirdPartyAuthInfo instance with given third-party user id and provider.
     *
     * @param  string $thirdPartyUserId
     * @param  string $provider
     * @return App\ThirdPartyAuthInfo|null
     */
    public function retrieveThirdPartyAuthInfo($thirdPartyUserId, $provider)
    {
        return ThirdPartyAuthInfo::where('third_party_user_id', $thirdPartyUserId)->where('third_party', $provider)->first();
    }

    /**
     * Find user with the given email.
     *
     * @param  string $email
     * @return  App\User|null
     */
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }
}
