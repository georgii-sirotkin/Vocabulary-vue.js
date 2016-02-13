<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ThirdPartyAuthService;
use Illuminate\Http\Request;

class ThirdPartyAuthController extends Controller
{
    private $thirdPartyAuth;
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param App\Services\ThirdPartyAuthService $thirdPartyAuth
     * @return void
     */
    public function __construct(ThirdPartyAuthService $thirdPartyAuth, Request $request)
    {
        $this->middleware('guest');
        $this->thirdPartyAuth = $thirdPartyAuth;
        $this->request = $request;
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
     * @param  string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        $this->checkProvider($provider);

        if ($this->isAccessDenied()) {
            return redirect('/');
        }

        if (!$this->hasProviderSentAnyData()) {
            return redirect()->route('third_party_login', ['provider' => $provider]);
        }

        $this->thirdPartyAuth->handleCallback($provider);
        return redirect()->intended('/home');
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
     * @return boolean
     */
    private function isAccessDenied()
    {
        return $this->request->error == 'access_denied';
    }

    /**
     * Determine if provider has sent any data.
     *
     * @return boolean
     */
    private function hasProviderSentAnyData()
    {
        return !empty($this->request->query());
    }
}
