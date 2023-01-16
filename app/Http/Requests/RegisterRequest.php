<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email:strict,filter', 'unique:App\Models\User,email'],
            'password' => [
                'required',
                'confirmed',
                'not_regex:/password$/i',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}
