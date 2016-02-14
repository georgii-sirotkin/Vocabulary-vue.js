<?php

namespace App\Repositories;

use App\ThirdPartyAuthInfo;

class UserRepository
{

    /**
     * Retrieve ThirdPartyAuthInfo instance with given third-party user id and provider.
     *
     * @param  string $thirdPartyUserId
     * @param  string $provider
     * @return App\ThirdPartyAuthInfo
     */
    public function retrieveThirdPartyAuthInfo($thirdPartyUserId, $provider)
    {
        return ThirdPartyAuthInfo::where('third_party_user_id', $thirdPartyUserId)->where('third_party', $provider)->first();
    }
}
