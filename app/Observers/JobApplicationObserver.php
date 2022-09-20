<?php

namespace App\Observers;

use App\Models\CandidateProfile;
use App\Models\JobApplication;
use App\Models\Notification;

class JobApplicationObserver
{
    /**
     * Handle the JobApplication "saved" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function saved(JobApplication $jobApplication)
    {
        $this->createNotification($jobApplication);
    }

    /**
     * Handle the JobApplication "created" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function created(JobApplication $jobApplication)
    {
        //
    }

    /**
     * Handle the JobApplication "updated" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function updated(JobApplication $jobApplication)
    {
        //
    }

    /**
     * Handle the JobApplication "deleted" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function deleted(JobApplication $jobApplication)
    {
        //
    }

    /**
     * Handle the JobApplication "restored" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function restored(JobApplication $jobApplication)
    {
        //
    }

    /**
     * Handle the JobApplication "force deleted" event.
     *
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return void
     */
    public function forceDeleted(JobApplication $jobApplication)
    {
        //
    }

    public function createNotification(JobApplication $jobApplication)
    {
        switch ($jobApplication->application_status) {
            case JobApplication::STATUS_PENDING:
                $key = 'pending';
                break;
            case JobApplication::STATUS_SHORTLISTED:
                $key = 'shortlisted';
                break;
            case JobApplication::STATUS_HIRED:
                $key = 'hired';
                break;
            case JobApplication::STATUS_REJECTED:
                $key = 'rejected';
                break;
            default:
                $key = 'others';
                break;
        }

        Notification::create([
            'header_key' => "notification.header.application.status.update.{$key}",
            'content_key' => "notification.content.application.status.update.{$key}",
            'trans_attributes' => [
                'status' => '__job_application.status_' . strtolower($jobApplication->application_status),
                'candidateFirstName' => $jobApplication->candidate->user->first_name,
                'candidateLastName' => $jobApplication->candidate->user->first_name,
                'jobRole' => $jobApplication->jobVacancy->name,
                'companyName' => $jobApplication->jobVacancy->company->name,
            ],
            'notifiable_id' => $jobApplication->candidate_id,
            'notifiable_type' => CandidateProfile::class,
        ]);
    }
}
