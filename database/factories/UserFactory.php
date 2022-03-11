<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email'             => $this->faker->unique()->email(),
            'first_name'        => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'password'          => bcrypt('userpassword'),
            'address'           => $this->faker->address(),
            'avatar'            => $this->faker->uuid(),
            'phone_number'      => $this->faker->phoneNumber(),
            'is_marketing'      => $this->faker->boolean(15),
            'is_admin'          => 0,
            'email_verified_at' => now(),
        ];
    }
    
    public function admin()
    {
        return $this->state(function(array $attributes) {
            return [
                'is_admin' => 1,
                'password' => bcrypt('admin'),
            ];
        });
    }
}
