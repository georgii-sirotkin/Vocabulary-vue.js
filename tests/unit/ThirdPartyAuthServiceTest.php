<?php

use App\Services\ThirdPartyAuthService;

class ThirdPartyAuthServiceTest extends PHPUnit_Framework_TestCase
{
    private $mockedSocialite;
    private $mockedGuard;
    private $mockedRegistrationService;
    private $mockedRepository;
    private $authService;
    private $mockedProvider;
    private $mockedUser;

    public function setUp()
    {
        $this->mockedSocialite = Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->mockedGuard = Mockery::mock('Illuminate\Contracts\Auth\Factory');
        $this->mockedRegistrationService = Mockery::mock('App\Services\RegistrationService');
        $this->mockedRepository = Mockery::mock('App\Repositories\UserRepository');
        $this->authService = new ThirdPartyAuthService(['facebook'], $this->mockedSocialite, $this->mockedGuard, $this->mockedRegistrationService, $this->mockedRepository);
        $this->mockedProvider = Mockery::mock('\Laravel\Socialite\Contracts\Provider');
        $this->mockedUser = Mockery::mock('Laravel\Socialite\Contracts\User');

        $this->mockedSocialite->shouldReceive('driver')
            ->with('facebook')
            ->once()
            ->andReturn($this->mockedProvider);

        $this->mockedProvider->shouldReceive('user')
            ->once()
            ->andReturn($this->mockedUser);

        $this->mockedUser->shouldReceive('getId')
            ->once()
            ->andReturn('5');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function logins_user()
    {
        $mockedThirdPartyAuthInfo = Mockery::mock('App\ThirdPartyAuthInfo');

        $this->mockedRepository->shouldReceive('retrieveThirdPartyAuthInfo')
            ->with(5, 'facebook')
            ->once()
            ->andReturn($mockedThirdPartyAuthInfo);

        $mockedThirdPartyAuthInfo->shouldReceive('getAttribute')
            ->with('user')
            ->andReturn($this->mockedUser);

        $this->mockedGuard->shouldReceive('login')
            ->with($this->mockedUser)
            ->once();

        $this->mockedRegistrationService->shouldNotReceive('register');

        $this->authService->handleCallback('facebook');
    }

    /** @test */
    public function registers_user()
    {
        $this->mockedRepository->shouldReceive('retrieveThirdPartyAuthInfo')
            ->with(5, 'facebook')
            ->once()
            ->andReturn(null);

        $this->mockedUser->shouldReceive('getEmail')
            ->times(2)
            ->andReturn('john@example.com');

        $this->mockedUser->shouldReceive('getName')
            ->times(2)
            ->andReturn('John Doe');

        $this->mockedUser->shouldReceive('getId')
            ->once()
            ->andReturn('123456');

        $this->mockedRegistrationService->shouldReceive('register')
            ->with(Mockery::type('array'), true, Mockery::type('array'))
            ->once()
            ->andReturn($this->mockedUser);

        $this->mockedGuard->shouldReceive('login')
            ->with($this->mockedUser)
            ->once();

        $this->authService->handleCallback('facebook');
    }

}
