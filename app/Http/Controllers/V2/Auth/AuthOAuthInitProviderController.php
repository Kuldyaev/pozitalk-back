<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Actions\Auth\AuthTelegram\AuthOAuthTelegramCreateLinkAction;
use App\Http\Requests\Auth\AuthOAuthGetLinkRequest;
use App\Models\Auth\AuthProviderEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Stringable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class AuthOAuthInitProviderController extends AuthController
{
    #[Post(
        tags: ['Authentification'],
        operationId: 'GetOAuthInit',
        path: '/auth/{AuthProviderType}/oauth-init',
        summary: 'Get OAuth link',
        description: 'Ссылка для авторизации через внешний сервис',
        parameters: [
            new Parameter(
                name: 'AuthProviderType',
                in: 'path',
                example: 'telegram',
                description: 'Тип провайдера авторизации (telegram)'
            ),
            new Parameter(
                name: 'referal_invited',
                in: 'query',
                example: 'ODkwOTI5MjkyOTI=',
                description: 'Referal invited code',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(
                    type: 'object',
                    oneOf: [
                        new Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new Property(property: 'botId', type: 'number', example: 45785236),
                            ]
                        )
                    ]
                ),
            )
        ]
    )]
    final public function __invoke(
        AuthOAuthGetLinkRequest $request,
        AuthProviderEnum $provider
    ): JsonResponse {

        // TODO: FEATURE_AUTHBOT_CAST
        $isCastDomain = env('FEATURE_AUTHBOT_CAST')
            ? (new Stringable($request->header('Origin')))
                ->contains(env('FEATURE_AUTHBOT_CAST_DOMAIN'))
            : false;

        $authProvider = match ($provider) {
            AuthProviderEnum::TELEGRAM => app(AuthOAuthTelegramCreateLinkAction::class)
                ->run($request->input('referal_invited'), $isCastDomain),
        };

        return $this->setCookies(
            new JsonResponse(['data' => $authProvider->data]),
            app(AuthCookieFromAuthProviderAction::class)->run($authProvider)
        );
    }
}
