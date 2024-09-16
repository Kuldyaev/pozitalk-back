<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Sanctum\PersonalAccessToken;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @mixin NewAccessToken
 */
class AuthTokenResource extends JsonResource
{

    #[Schema(
        type: 'object',
        schema: 'AuthTokenResource',
        description: 'Схема токена авторизации',
        properties: [
            new Property(
                property: 'token',
                type: 'string',
                example: 'Aoenkru@#$w4tekhnstakoen3w2',
                description: 'Токен авторизации',
            ),
            new Property(
                property: 'expires_in',
                type: 'datetime',
                example: '2024-04-02T12:03:59.000000Z',
                description: 'Время устаревания токена',
            ),
        ]
    )]
    public function toArray($request): array
    {
        return [
            'token' => $this->plainTextToken,
            'expires_in' => $this->accessToken->expires_at,
        ];
    }
}
