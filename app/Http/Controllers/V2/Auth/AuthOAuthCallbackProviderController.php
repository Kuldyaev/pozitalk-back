<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Actions\Auth\AuthTelegram\AuthOAuthTelegramAction;
use App\Http\Requests\Auth\AuthOAuthCallbackRequest;
use App\Models\Auth\AuthProviderEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Stringable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use React\Stream\DuplexResourceStream;

class AuthOAuthCallbackProviderController extends AuthController
{
    #[Post(
        tags: ['Authentification'],
        operationId: 'OAuthCallback',
        path: '/auth/{AuthProviderType}/oauth-callback',
        summary: 'OAuth callback proccessing',
        description: 'Обработка ответа от провайдера авторизации',
        parameters: [
            new Parameter(
                name: 'AuthProviderType',
                in: 'path',
                example: 'telegram',
            )
        ],
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: '#/components/schemas/AuthOAuthCallbackRequest'
            ),
        ),
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            ),
            new Response(
                response: 422,
                description: 'Unprocessable Content',
                content: new JsonContent(
                    properties: [
                        new Property(
                            property: 'message',
                            type: 'string',
                            description: 'Incorrect email or password'
                        ),
                    ]
                )
            )
        ]
    )]
    public function __invoke(AuthOAuthCallbackRequest $request, AuthProviderEnum $provider)
    {
        // TODO: FEATURE_AUTHBOT_CAST
        $isCastDomain = env('FEATURE_AUTHBOT_CAST')
            ? (new Stringable($request->header('Origin')))
                ->contains(env('FEATURE_AUTHBOT_CAST_DOMAIN'))
            : false;

        $authProvider = match ($provider) {
            AuthProviderEnum::TELEGRAM => app(AuthOAuthTelegramAction::class)
                ->run($request->user(), $request->input('data'), $isCastDomain)
        };

        return $this->setCookies(
            new JsonResponse(),
            app(AuthCookieFromAuthProviderAction::class)->run($authProvider)
        );
    }
}
