<?php

namespace App\Http\Requests\Company\Auth;

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
            'name' => ['required', 'string', 'max:100'],
            'description' => ['string', 'max:100'],
            'address' => ['required', 'string', 'max:200'],
            'date_of_establishment' => ['required', 'date', 'date_format:Y-m-d', 'before:' . now()],
        ];
    }
}
