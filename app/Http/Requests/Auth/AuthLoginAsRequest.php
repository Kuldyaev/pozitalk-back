<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use App\Models\User;
use Illuminate\Validation\Rules\Exists;

class AuthLoginAsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', new Exists(User::class, 'id')],
        ];
    }
}
