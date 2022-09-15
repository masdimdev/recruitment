<?php

namespace App\Http\Requests\Candidate\Auth;

use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'phone_number' => preg_replace("/[^0-9]/", '', $this->input('phone_number', '')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique(User::class)],
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
            'phone_number' => [
                'required',
                'digits_between:6,16',
                Rule::unique(CandidateProfile::class),
            ],
            'address' => ['required', 'string', 'max:200'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d', 'before:' . now()],
            'sex' => ['required', Rule::in(CandidateProfile::SEX_MALE, CandidateProfile::SEX_FEMALE)],
        ];
    }
}
