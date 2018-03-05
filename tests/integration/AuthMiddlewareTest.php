<?php

class AuthMiddlewareTest extends TestCase
{
    /** @test */
    public function guest_cant_view_protected_area()
    {
        $this->visit(route('home'))->seePageIs('/login');
        $this->visit(route('words.index'))->seePageIs('/login');
        $this->visit(route('random_word'))->seePageIs('/login');
    }
}
