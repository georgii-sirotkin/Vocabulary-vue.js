<?php

namespace App\Http\Controllers;

use App\Services\ThirdPartyAuthService;
use Illuminate\Http\Request;

class ThirdPartyAuthController extends Controller
{
    /**
     * @var ThirdPartyAuthService
     */
    private $thirdPartyAuth;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ThirdPartyAuthService $thirdPartyAuth
     */
    public function __construct(ThirdPartyAuthService $thirdPartyAuth)
    {
        $this->thirdPartyAuth = $thirdPartyAuth;
    }

    /**
     * Redirect the user to the third party authentication page.
     *
     * @param  string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        $this->checkProvider($provider);
        return $this->thirdPartyAuth->redirectToProvider($provider);
    }

    /**
     * Obtain the user information from the third party and authenticate user.
     *
     * @param Request $request
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $this->checkProvider($provider);

        if ($this->isAccessDenied($request) || !$this->hasProviderSentAnyData($request)) {
            return redirect()->route('login');
        }

        $this->thirdPartyAuth->handleCallback($provider);

        return redirect()->intended(route('home'));
    }

    /**
     * Check that provider is supported.
     *
     * @param  string $provider
     * @return void
     */
    private function checkProvider($provider)
    {
        if (!$this->thirdPartyAuth->checkProvider($provider)) {
            abort(404);
        }
    }

    /**
     * Determine if user denied access.
     *
     * @param Request $request
     * @return bool
     */
    private function isAccessDenied(Request $request)
    {
        return $request->has('error') || $request->has('error_code') || $request->has('error_message');
    }

    /**
     * Determine if provider has sent any data.
     *
     * @param Request $request
     * @return bool
     */
    private function hasProviderSentAnyData(Request $request)
    {
        return !empty($request->query());
    }
}
