<?php

class ThirdPartyAuthTest extends TestCase
{
    /** @test */
    public function page_not_found_for_unsupported_providers()
    {
        $this->setExpectedException('Illuminate\Foundation\Testing\HttpException');
        $this->visit('/login/unsupported_provider');

        $this->setExpectedException('Illuminate\Foundation\Testing\HttpException');
        $this->visit('/login/unsupported_provider/callback');
    }

    /** @test */
    public function redirects_to_facebook()
    {
        try {
            $this->visit('/login/facebook');
        } catch (Exception $e) {}

        $this->assertContains('www.facebook.com', $this->currentUri);
    }
}
