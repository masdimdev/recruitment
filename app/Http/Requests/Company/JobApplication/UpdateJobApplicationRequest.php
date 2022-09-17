<?php

namespace App\Http\Requests\Company\JobApplication;

use App\Models\JobApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobApplicationRequest extends FormRequest
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
            'status' => [
                'required',
                Rule::in([
                    JobApplication::STATUS_PENDING,
                    JobApplication::STATUS_SHORTLISTED,
                    JobApplication::STATUS_INTERVIEW,
                    JobApplication::STATUS_HIRED,
                    JobApplication::STATUS_REJECTED,
                ])
            ]
        ];
    }
}
