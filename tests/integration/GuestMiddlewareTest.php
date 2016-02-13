<?php

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GuestMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function authenticated_user_redirected_to_home_page()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user)
            ->visit('/login')
            ->seePageIs(route('home'));

        $this->visit('/register')
            ->seePageIs(route('home'));

        $this->visit('/login/facebook')
            ->seePageIs(route('home'));
    }
}
