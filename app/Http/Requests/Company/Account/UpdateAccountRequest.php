<?php

namespace App\Http\Requests\Company\Account;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
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
            'email' => ['email', Rule::unique(User::class)->ignore($this->user())],
            'new_password' => ['min:6'],
            'new_password_confirmation' => ['required_with:new_password', 'same:new_password'],
            'current_password' => ['required_with:email,new_password', 'current_password'],
        ];
    }
}
