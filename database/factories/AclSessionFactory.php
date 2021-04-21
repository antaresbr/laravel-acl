<?php

namespace Antares\Acl\Database\Factories;

use Antares\Acl\Models\AclSession;
use Antares\Acl\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AclSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AclSession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'api_token' => Str::random(128),
            'valid' => $this->faker->boolean(),
            'user_id' => User::all(['id'])->random(),
            'issued_at' => $this->faker->dateTime(),
            'expires_at' => $this->faker->dateTime(),
        ];
    }
}
