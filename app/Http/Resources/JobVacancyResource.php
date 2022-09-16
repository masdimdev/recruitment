<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => (bool)$this->is_active,
            'job_type' => $this->job_type,
            'job_category' => new JobCategoryResource($this->jobCategory),
            'company' => new CompanyProfileResource($this->company),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
