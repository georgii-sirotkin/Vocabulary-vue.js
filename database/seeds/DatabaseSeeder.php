<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const USERS_WITH_PASSWORDS_NUMBER = 7;
    const USERS_WITH_THIRD_PARTY_AUTH_NUMBER = 3;
    const MAX_NUMBER_OF_WORDS_PER_USER = 10;
    const MAX_NUMBER_OF_DEFINITIONS_PER_WORD = 4;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = $this->createUsers();
        foreach ($users as $user) {
            $this->createWordsForUser($user);
        }
    }

    /**
     * Generate users.
     *
     * @return mixed
     */
    private function createUsers()
    {
        $users = $this->createUsersWithPasswords(self::USERS_WITH_PASSWORDS_NUMBER);
        return $users->merge($this->createUsersWithThirdPartyAuth(self::USERS_WITH_THIRD_PARTY_AUTH_NUMBER));
    }

    /**
     * Create users authenticated by email and password.
     *
     * @param  int $number
     * @return  mixed
     */
    private function createUsersWithPasswords($number)
    {
        return factory(App\User::class, $number)->create();
    }

    /**
     * Create users authenticated by third party.
     *
     * @param  integer $number
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function createUsersWithThirdPartyAuth($number)
    {
        return factory(App\User::class, $number)->create(['password' => null])->each(function ($user) {
            $user->thirdPartyAuths()->save(factory(App\ThirdPartyAuthInfo::class)->make());
        });
    }

    /**
     * Create words for a given user.
     *
     * @param  App\User $user
     * @return void
     */
    private function createWordsForUser($user)
    {
        $words = $user->words()->saveMany($this->generateWords(rand(0, self::MAX_NUMBER_OF_WORDS_PER_USER)));
        foreach ($words as $word) {
            $this->createDefinitionsForWord($word);
        }
    }

    /**
     * Generate words.
     *
     * @param  int $number
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function generateWords($number)
    {
        $words = factory(App\Word::class, $number)->make();
        if ($number == 1) {
            $words = new Collection([$words]);
        }
        return $words;
    }

    /**
     * Create definitions for a given word.
     *
     * @param  App\Word $word
     * @return void
     */
    private function createDefinitionsForWord($word)
    {
        $word->definitions()->saveMany($this->generateDefinitions(rand(0, self::MAX_NUMBER_OF_DEFINITIONS_PER_WORD)));
    }

    /**
     * Generate definitions.
     *
     * @param  integer $number
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function generateDefinitions($number)
    {
        $definitions = factory(App\Definition::class, $number)->make();
        if ($number == 1) {
            $definitions = new Collection([$definitions]);
        }
        return $definitions;
    }
}
