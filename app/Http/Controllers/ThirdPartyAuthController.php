<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ThirdPartyAuthService;
use Illuminate\Http\Request;
use Socialite;

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
     * Obtain the user information from the third party.
     *
     * @param  string $provider
     * @return [type]           [description]
     */
    public function handleProviderCallback($provider)
    {
        $this->checkProvider($provider);

        if ($this->isAccessDenied()) {
            return redirect('/');
        }

        dd($this->hasProviderSentAnyData());
        dd($request->query);

        $user = Socialite::driver($provider)->user();
        dd($user);
        // check provider
        // check request
        // try to get user
        // if user exists then login  StatefulGuard login
        // create new user
        //
        // ThirdPartyAuthService
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

    private function hasProviderSentAnyData()
    {
        return $this->request->query()->count(); // !== 0;
    }
}
