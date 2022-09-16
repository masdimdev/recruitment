<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobVacancyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->jobTitle,
            'description' => $this->faker->sentence(),
            'is_active' => 1,
            'job_type' => $this->faker->randomElement([
                JobVacancy::TYPE_INTERNSHIP,
                JobVacancy::TYPE_FREELANCE,
                JobVacancy::TYPE_FULL_TIME,
                JobVacancy::TYPE_PART_TIME,
            ]),
            'job_category_id' => JobCategory::inRandomOrder()->first()->id,
            'company_id' => CompanyProfile::factory(),
        ];
    }
}
