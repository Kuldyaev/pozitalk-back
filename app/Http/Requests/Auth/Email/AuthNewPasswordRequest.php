<?php
declare(strict_types=1);
namespace App\Http\Requests\Auth\Email;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Hash;

class AuthNewPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                fn($attr, $value, $fail) => Hash::check(
                    $value,
                    $this->user()->getAttribute('password')
                ) || $fail('The current password is incorrect.')
            ],
            'password_new' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
