<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

class AuthLoginRequest extends FormRequest
{
    #[Schema(
        schema: 'AuthLoginEmailRequest',
        title: 'Login with email request',
        type: 'object',
        required: ['email', 'password'],
        properties: [
            new Property(
                property: 'email',
                type: 'string',
                format: 'email',
                example: 'email@example.com',
            ),
            new Property(
                property: 'password',
                type: 'string',
                format: 'password',
                example: 'pwd12oe@rw34',
            )
        ]
    )]
    public function rules(): array
    {
        return [
            'email' => ['required', 'exists:users,email'],
            'password' => ['required']
        ];
    }
}
