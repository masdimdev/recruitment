<?php

namespace App\Http\Requests\Company\JobVacancy;

use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobVacancyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required'],
            'is_active' => ['required', 'boolean'],
            'job_type' => [
                'required',
                Rule::in(
                    JobVacancy::TYPE_FREELANCE,
                    JobVacancy::TYPE_PART_TIME,
                    JobVacancy::TYPE_INTERNSHIP,
                    JobVacancy::TYPE_FULL_TIME,
                )
            ],
            'job_category_id' => ['required', Rule::exists(JobCategory::class, 'id')],
        ];
    }
}
