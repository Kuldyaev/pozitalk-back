<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

class AuthOAuthCallbackRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::user()->tokenCan('auth:provider');
    }

    #[Schema(
        schema: 'AuthOAuthCallbackRequest',
        type: 'object',
        properties: [
            new Property(
                property: 'data',
                type: 'string',
                example: 'SomeEncodedStringFromProvider',
            )
        ]
    )]
    public function rules(): array
    {
        return [
            'data' => 'required'
        ];
    }
}
