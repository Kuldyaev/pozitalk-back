<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Actions\Auth\AuthFindToken;
use App\Actions\Auth\AuthResendCodeWithEmailAction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;

class AuthResendCodeController extends AuthController
{
    #[Post(
        tags: ['Authentification'],
        path: '/auth/{AuthProviderType}/resend-code',
        summary: 'Resend code for authentication (use refresh token)',
        description: 'Повторная отправка кода для аутентификации (использует refresh token)',
        parameters: [
            new Parameter(
                name: 'AuthProviderType',
                in: 'path',
                example: 'email',
                description: 'Провайдер авторизации (email)',
            ),
        ],
        responses: [
            new Response(response: 200, description: 'OK'),
            new Response(response: 401, description: 'Unauthenticated'),
            new Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function __invoke(Request $request, string $provider): JsonResponse
    {
        $token = $request->cookie('refresh_token')
            ? app(AuthFindToken::class)->run($request->cookie('refresh_token'))
            : null;

        if (is_null($token)) {
            throw new AuthenticationException();
        }

        if ($token->cant('auth:resend-code')) {
            throw new AuthorizationException();
        }

        //** @var AuthProvider $authProvider */
        $authProvider = $token->tokenable
            ->authProviders()
            ->where('provider', $provider)
            ->firstOrFail();

        app(AuthResendCodeWithEmailAction::class)->run($authProvider);

        return $this->setCookies(
            new JsonResponse(),
            app(AuthCookieFromAuthProviderAction::class)->run($authProvider)
        );
    }
}
