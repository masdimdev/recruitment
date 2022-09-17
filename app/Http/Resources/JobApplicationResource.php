<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'cover_letter' => $this->cover_letter,
            'application_status' => $this->application_status,
            'job_vacancy' => new JobVacancyResource($this->jobVacancy),
            'candidate' => new CandidateProfileResource($this->candidate),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
