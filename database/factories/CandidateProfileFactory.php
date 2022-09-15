<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'phone_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'sex' => 1, // Male
            'user_id' => User::factory(),
        ];
    }
}
