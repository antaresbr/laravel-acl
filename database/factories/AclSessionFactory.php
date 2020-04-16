<?php

use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(AclSession::class, function (Faker $faker) {
    return [
        'api_token' => Str::random(128),
        'valid' => $faker->boolean(),
        'user_id' => User::all(['id'])->random(),
        'issued_at' => $faker->dateTime(),
        'expires_at' => $faker->dateTime(),
    ];
});
