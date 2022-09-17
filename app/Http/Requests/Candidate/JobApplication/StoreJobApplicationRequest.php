<?php

namespace App\Http\Requests\Candidate\JobApplication;

use App\Models\JobVacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobApplicationRequest extends FormRequest
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
            'cover_letter' => ['nullable', 'string'],
            'job_vacancy_id' => [
                'required',
                Rule::exists(JobVacancy::class, 'id')->where('is_active', true)
            ],
        ];
    }
}
