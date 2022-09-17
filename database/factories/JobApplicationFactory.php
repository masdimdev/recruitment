<?php

namespace Database\Factories;

use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cover_letter' => function (array $attributes) {
                return rand(1, 10) % 2 == 1 ? $this->faker->sentences(2, true) : null;
            },
            'application_status' => $this->faker->randomElement([
                JobApplication::STATUS_PENDING,
                JobApplication::STATUS_SHORTLISTED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_HIRED,
                JobApplication::STATUS_REJECTED,
            ]),
            'job_vacancy_id' => function (array $attributes) {
                $jobs = JobVacancy::inRandomOrder()->first();

                return $jobs ? $jobs->id : JobVacancy::factory();
            },
            'candidate_id' => CandidateProfile::factory(),
        ];
    }
}
