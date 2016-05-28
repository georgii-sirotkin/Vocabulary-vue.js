<?php

class ThirdPartyAuthTest extends TestCase
{
    /** @test */
    public function page_not_found_for_unsupported_providers()
    {
        $this->setExpectedException('Illuminate\Foundation\Testing\HttpException');
        $this->visit(route('third_party_login', ['provider' => 'facebookunsupported_provider']));

        $this->setExpectedException('Illuminate\Foundation\Testing\HttpException');
        $this->visit(route('third_party_login_callback', ['provider' => 'facebookunsupported_provider']));
    }

    /** @test */
    public function redirects_to_root_if_access_denied()
    {
        $this->visit(route('third_party_login_callback', ['provider' => 'facebook']) . '?error=access_denied')
            ->seePageIs('/');
    }
}
