<?php

namespace App\Http\Requests\Company\Profile;

use Illuminate\Foundation\Http\FormRequest;

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
            'name' => ['string', 'max:100'],
            'description' => ['string', 'max:100'],
            'address' => ['string', 'max:200'],
            'date_of_establishment' => ['date', 'date_format:Y-m-d', 'before:' . now()],
        ];
    }
}
