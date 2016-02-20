<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\ThirdPartyAuthInfo::class, function (Faker\Generator $faker) {
    return [
        'third_party' => $faker->randomElement(config('settings.authentication_services')),
        'third_party_user_id' => $faker->unique()->randomNumber(8),
    ];
});

$factory->define(App\Word::class, function (Faker\Generator $faker) {
    $word = $faker->unique()->word;
    return [
        'word' => $word,
        'slug' => $word,
        'right_guesses_number' => $faker->randomDigit,
        'image_filename' => $faker->optional(0.3)->word,
    ];
});

$factory->define(App\Definition::class, function (Faker\Generator $faker) {
    return [
        'definition' => $faker->sentence(),
    ];
});
