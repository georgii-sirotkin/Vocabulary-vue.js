<?php

namespace App\Services;

use App\Services\RegistrationService;
use App\ThirdPartyAuthInfo;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\User;

class ThirdPartyAuthService
{
    private $supportedProviders;
    private $socialite;
    private $guard;
    private $registrationService;

    /**
     * Create a new instance of service.
     *
     * @param array $supportedProviders
     */
    public function __construct(array $supportedProviders, Factory $socialite, AuthFactory $guard, RegistrationService $registrationService)
    {
        $this->supportedProviders = $supportedProviders;
        $this->socialite = $socialite;
        $this->guard = $guard;
        $this->registrationService = $registrationService;
    }

    /**
     * Check that provider is one of the supported providers.
     *
     * @param  string $provider
     * @return bool
     */
    public function checkProvider($provider)
    {
        return in_array($provider, $this->supportedProviders);
    }

    /**
     * Redirect the user to the third party authentication page.
     *
     * @param  string $provider
     * @return void
     */
    public function redirectToProvider($provider)
    {
        return $this->socialite->driver($provider)->redirect();
    }

    /**
     * Login or register user.
     *
     * @param  string $provider
     * @return void
     */
    public function handleCallback($provider)
    {
        $thirdPartyUser = $this->getThirdPartyUser($provider);
        $authInfo = $this->retrieveThirdPartyAuthInfo($thirdPartyUser, $provider);

        if ($authInfo) {
            $user = $authInfo->user;
        } else {
            $user = $this->register($thirdPartyUser, $provider);
        }

        $this->guard->login($user);
    }

    /**
     * Get user instance from third party auth provider.
     *
     * @param  string $provider
     * @return Laravel\Socialite\Contracts\User
     */
    private function getThirdPartyUser($provider)
    {
        return $this->socialite->driver($provider)->user();
    }

    /**
     * Get ThirdPartyAuthInfo instance with the given id and auth provider.
     *
     * @param  Laravel\Socialite\Contracts\User   $thirdPartyUser
     * @param string $provider
     * @return App\ThirdPartyAuthInfo|null
     */
    private function retrieveThirdPartyAuthInfo(User $thirdPartyUser, $provider)
    {
        return ThirdPartyAuthInfo::where('third_party_user_id', $thirdPartyUser->getId())->where('third_party', $provider)->first();
    }

    /**
     * Register user.
     *
     * @param  Laravel\Socialite\Contracts\User   $thirdPartyUser
     * @param  string $provider
     * @return App\User
     */
    private function register(User $thirdPartyUser, $provider)
    {
        $data = $this->prepareUserData($thirdPartyUser);

        $thirdPartyAuthData = $this->prepareThirdPartyAuthData($thirdPartyUser, $provider);

        return $this->registrationService->register($data, true, $thirdPartyAuthData);
    }

    /**
     * Get array of user data.
     *
     * @param  Laravel\Socialite\Contracts\User   $thirdPartyUser
     * @return array
     */
    private function prepareUserData(User $thirdPartyUser)
    {
        $data = [
            'name' => $thirdPartyUser->getName(),
        ];

        if ($this->emailIsValid($thirdPartyUser->getEmail())) {
            $data['email'] = $thirdPartyUser->getEmail();
        }

        return $data;
    }

    /**
     * Get array of third party auth data.
     *
     * @param  Laravel\Socialite\Contracts\User   $thirdPartyUser
     * @param  string  $provider
     * @return array
     */
    private function prepareThirdPartyAuthData(User $thirdPartyUser, $provider)
    {
        return [
            'third_party' => $provider,
            'third_party_user_id' => $thirdPartyUser->getId(),
        ];
    }

    /**
     * Determine if email is valid.
     *
     * @param  string $email
     * @return bool
     */
    private function emailIsValid($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
