<?php

namespace App\Services;

use Laravel\Socialite\Contracts\Factory;

class ThirdPartyAuthService
{
    private $supportedProviders;
    private $socialite;

    /**
     * Create a new instance of service.
     *
     * @param array $supportedProviders
     */
    public function __construct(array $supportedProviders, Factory $socialite)
    {
        $this->supportedProviders = $supportedProviders;
        $this->socialite = $socialite;
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
}
