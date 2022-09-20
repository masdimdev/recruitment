<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Observers\JobApplicationObserver;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $observer = new JobApplicationObserver();
        $jobApplications = JobApplication::all();
        foreach ($jobApplications as $jobApplication) {
            $observer->createNotification($jobApplication);
        }
    }
}
