<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory([
            'user_type' => User::TYPE_COMPANY
        ]);
        $user->afterCreating = collect([]);

        return [
            'name' => $this->faker->company,
            'description' => $this->faker->text(10),
            'address' => $this->faker->address,
            'date_of_establishment' => $this->faker->date(),
            'user_id' => $user,
        ];
    }
}
