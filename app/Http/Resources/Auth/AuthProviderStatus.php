<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\NewAccessToken;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use phpDocumentor\Reflection\Types\This;

/**
 * @mixin AuthProvider
 * @property NewAccessToken|null $accessToken
 * @property NewAccessToken|null $refreshToken
 */
class AuthProviderStatus extends JsonResource
{

    #[Schema(
        type: 'object',
        schema: 'AuthProviderStatus',
        title: 'Auth provider status',
        description: 'Статус провайдера авторизации',
        properties: [
            new Property(
                property: 'id',
                type: 'integer',
                example: '2',
                description: 'Идентификатор провайдера авторизации',
            ),
            new Property(
                property: 'provider',
                type: 'string',
                example: 'email',
                description: 'Название провайдера авторизации',
            ),
            new Property(
                property: 'status',
                type: 'string',
                example: 'sent-code',
                description: 'Статус провайдера авторизации',
            )
        ]
    )]
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'provider' => $this->provider,
            'status' => $this->status,
        ];

        if ($this->provider === AuthProviderEnum::TELEGRAM) {
            $data['url'] = $this->data['url'];
        }

        return $data;
    }

    public function withResponse($request, $response): void
    {
        if ($this->accessToken) {
            $response->withCookie(
                cookie(
                    'accessToken',
                    $this->accessToken->plainTextToken,
                    Config::tokenTimeout($this->accessToken->accessToken->name)
                )
            );

        }

        if ($this->refreshToken) {
            $response->withCookie(
                cookie(
                    'refreshToken',
                    $this->refreshToken->plainTextToken,
                    Config::tokenTimeout($this->refreshToken->accessToken->name)
                )
            );
        }
    }
}
