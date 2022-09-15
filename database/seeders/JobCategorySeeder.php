<?php

namespace Database\Seeders;

use App\Models\JobCategory;
use Illuminate\Database\Seeder;

class JobCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobCategories = [
            'Software Engineer',
            'Marketing',
            'Designer',
        ];

        foreach ($jobCategories as $jobCategory) {
            JobCategory::create([
                'name' => $jobCategory,
            ]);
        }
    }
}
