<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUsersWithPasswords(7000);
        $this->createUsersWithThirdPartyAuth(3000);
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
     * @return mixed
     */
    private function createUsersWithThirdPartyAuth($number)
    {
        return factory(App\User::class, $number)->create(['password' => null])->each(function ($user) {
            $user->thirdPartyAuths()->save(factory(App\ThirdPartyAuthInfo::class)->make());
        });
    }
}
