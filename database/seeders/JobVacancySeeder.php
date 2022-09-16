<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\JobVacancy;
use Illuminate\Database\Seeder;

class JobVacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        JobVacancy::factory()->count(rand(3, 7))->create([
            'company_id' => CompanyProfile::factory()->create()->id
        ]);

        JobVacancy::factory()->count(rand(3, 7))->create([
            'company_id' => CompanyProfile::factory()->create()->id
        ]);

        JobVacancy::factory()->count(rand(3, 7))->create([
            'company_id' => CompanyProfile::factory()->create()->id
        ]);
    }
}
