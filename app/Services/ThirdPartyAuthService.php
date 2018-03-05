<?php

namespace App\Services;

use App\ThirdPartyAuthInfo;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\User as ThirdPartyUser;
use DB;

class ThirdPartyAuthService
{
    private $supportedProviders;
    private $socialite;
    private $guard;

    /**
     * Create a new instance of service.
     *
     * @param array               $supportedProviders
     * @param Factory             $socialite
     * @param AuthFactory         $guard
     */
    public function __construct(array $supportedProviders, Factory $socialite, AuthFactory $guard)
    {
        $this->supportedProviders = $supportedProviders;
        $this->socialite = $socialite;
        $this->guard = $guard;
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
        } elseif ($this->emailIsValid($thirdPartyUser->getEmail()) && ($user = $this->getUserByEmail($thirdPartyUser->getEmail()))) {
            $this->addThirdPartyAuthInfoToUser($user, $thirdPartyUser, $provider);
        } else {
            $user = $this->registerNewUser($thirdPartyUser, $provider);
        }

        $this->guard->login($user, true);
    }

    /**
     * Get user instance from third party auth provider.
     *
     * @param  string $provider
     * @return ThirdPartyUser
     */
    private function getThirdPartyUser($provider)
    {
        return $this->socialite->driver($provider)->user();
    }

    /**
     * Get ThirdPartyAuthInfo instance with the given id and auth provider.
     *
     * @param  ThirdPartyUser   $thirdPartyUser
     * @param string $provider
     * @return ThirdPartyAuthInfo|null
     */
    private function retrieveThirdPartyAuthInfo(ThirdPartyUser $thirdPartyUser, $provider)
    {
        return ThirdPartyAuthInfo::where('third_party_user_id', $thirdPartyUser->getId())
            ->where('third_party', $provider)
            ->first();
    }

    /**
     * @param $email
     * @return User|null
     */
    private function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param User $user
     * @param ThirdPartyUser $thirdPartyUser
     * @param $provider
     */
    private function addThirdPartyAuthInfoToUser(User $user, ThirdPartyUser $thirdPartyUser, $provider)
    {
        $authInfo = new ThirdPartyAuthInfo([
            'third_party' => $provider,
            'third_party_user_id' => $thirdPartyUser->getId(),
        ]);

        $user->addThirdPartyAuthInfo($authInfo);
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

    /**
     * @param ThirdPartyUser $thirdPartyUser
     * @param $provider
     * @return User
     */
    private function registerNewUser(ThirdPartyUser $thirdPartyUser, $provider)
    {
        return DB::transaction(function () use ($thirdPartyUser, $provider) {
            $user = $this->createUser($thirdPartyUser);
            $this->addThirdPartyAuthInfoToUser($user, $thirdPartyUser, $provider);
            event(new Registered($user));
            return $user;
        });
    }

    /**
     * @param ThirdPartyUser $thirdPartyUser
     * @return User
     */
    private function createUser(ThirdPartyUser $thirdPartyUser) {
        return User::create([
            'email' => $this->emailIsValid($thirdPartyUser->getEmail()) ? $thirdPartyUser->getEmail() : null,
        ]);
    }
}
