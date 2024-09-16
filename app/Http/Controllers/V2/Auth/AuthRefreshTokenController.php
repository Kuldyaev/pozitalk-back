<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieRefreshTokenAction;
use App\Actions\Auth\AuthFindToken;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class AuthRefreshTokenController extends AuthController
{
    #[Get(
        tags: ['Authentification'],
        path: '/auth/refresh-token',
        summary: 'Обновление access_token по refresh, если отдает на refresh token 401, значит от тоже устарел',
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent()
            ),
            new Response(
                response: 401,
                description: 'Unauthenticated',
                content: new JsonContent()
            ),
            new Response(
                response: 403,
                description: 'Unauthorized',
                content: new JsonContent()
            )
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->cookie('refresh_token')
            ? app(AuthFindToken::class)->run($request->cookie('refresh_token'))
            : null;

        if (is_null($token)) {
            throw new AuthenticationException();
        }
        if ($token->cant('auth:refresh-token')) {
            throw new AuthorizationException();
        }

        return $this->setCookies(
            new JsonResponse(),
            [app(AuthCookieRefreshTokenAction::class)->run($token->tokenable)]
        );
    }
}
