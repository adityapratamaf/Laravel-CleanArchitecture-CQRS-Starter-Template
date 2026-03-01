<?php

namespace App\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:120'],
            'email' => ['required','email','max:190'],
            'password' => ['required','string','min:8'],
        ];
    }
}