<?php

namespace App\Http\Requests\Candidate\Profile;

use App\Models\CandidateProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'first_name' => ['string', 'max:100'],
            'last_name' => ['string', 'max:100'],
            'phone_number' => [
                'digits_between:6,16',
                Rule::unique(CandidateProfile::class)->ignore($this->user()->candidateProfile),
            ],
            'address' => ['string', 'max:200'],
            'date_of_birth' => ['date', 'date_format:Y-m-d', 'before:' . now()],
            'sex' => [Rule::in(CandidateProfile::SEX_MALE, CandidateProfile::SEX_FEMALE)],
        ];
    }
}
