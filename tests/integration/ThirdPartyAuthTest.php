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
    public function redirects_to_facebook()
    {
        try {
            $this->visit(route('third_party_login', ['provider' => 'facebook']));
        } catch (Exception $e) {}

        $this->assertContains('www.facebook.com', $this->currentUri);

        try {
            $this->visit(route('third_party_login_callback', ['provider' => 'facebook']));
        } catch (Exception $e) {}

        $this->assertContains('www.facebook.com', $this->currentUri);
    }

    /** @test */
    public function redirects_to_root_if_access_denied()
    {
        $this->visit(route('third_party_login_callback', ['provider' => 'facebook']) . '?error=access_denied')
            ->seePageIs('/');
    }

    /** @test */
    public function redirects_to_google()
    {
        try {
            $this->visit(route('third_party_login', ['provider' => 'google']));
        } catch (Exception $e) {}

        $this->assertContains('accounts.google.com', $this->currentUri);

        try {
            $this->visit(route('third_party_login_callback', ['provider' => 'google']));
        } catch (Exception $e) {}

        $this->assertContains('accounts.google.com', $this->currentUri);
    }
}
