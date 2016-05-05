<?php

use App\User;
use App\Word;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class WordTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $storage;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->actingAs($this->user);
    }

    protected function createWordForUser(array $data = array())
    {
        return $this->user->words()->save(factory(Word::class)->make($data));
    }
}
