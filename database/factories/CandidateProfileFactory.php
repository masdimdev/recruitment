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
        $user = User::factory([
            'user_type' => User::TYPE_CANDIDATE
        ]);
        $user->afterCreating = collect([]);

        return [
            'phone_number' => $this->faker->numerify('62#############'),
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'sex' => 1, // Male
            'user_id' => $user,
        ];
    }
}
