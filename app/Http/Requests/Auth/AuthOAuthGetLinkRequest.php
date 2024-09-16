<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @property string $referal_invited
 */
class AuthOAuthGetLinkRequest extends FormRequest
{
    #[Schema(
        schema: 'AuthOAuthGetLinkRequest',
        type: 'object',
        properties: [
            new Property(
                property: 'referal_invited',
                type: 'string',
                example: 'ODkwOTI5MjkyOTI=',
            ),
        ]
    )]
    public function rules(): array
    {
        return [
            'referal_invited' => ['nullable', 'string', 'exists:users,referal_invited'],
        ];
    }
}
