<?php

use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Event;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function user_can_register()
    {
        Event::fake();

        $this->visit('/register')
            ->type('john@example.com', 'email')
            ->type('123456', 'password')
            ->type('123456', 'password_confirmation')
            ->press('Register')
            ->seePageIs(route('home'))
            ->seeInDatabase('users', ['email' => 'john@example.com']);

        $this->assertEquals(1, User::count());
        $this->assertTrue(Auth::check());

        Event::assertFired(Registered::class);
        Event::assertFired(Login::class);
    }

    /** test */
    public function user_can_login()
    {
        $this->expectsEvents(Login::class);

        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('123456'),
        ]);

        $this->visit('/login')
            ->type('john@example.com', 'email')
            ->type('123456', 'password')
            ->press('Login')
            ->seePageIs(route('home'));

        $this->assertEquals($user, Auth::user());
    }

//    /** @test */
//    public function user_can_logout()
//    {
//        $user = factory(User::class)->create();
//
//        $this->actingAs($user)
//            ->visit(route('home'))
//            ->click('Log Out')
//            ->seePageIs('/');
//
//        $this->assertFalse(Auth::check());
//    }

    /** @test */
    public function user_has_a_limited_number_of_login_attempts()
    {
        $this->visit('/login');

        $attemptsMaxNumber = 5;
        for ($i = 0; $i < $attemptsMaxNumber + 1; $i++) {
            $this->type('john@example.com', 'email')
                ->type('incorrect password', 'password')
                ->press('Log In')
                ->seePageIs('/login');
        }
        $this->see('Too many login attempts');
    }

    // If user used third party authentication and hasn't set password, password is set to null
    /** @test */
    public function user_cant_login_if_password_is_null()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => null,
        ]);

        $this->visit('/login')
            ->type('john@example.com', 'email')
            ->type('123456', 'password')
            ->press('Log In')
            ->seePageIs('/login');

        $this->assertFalse(Auth::check());
    }

}
