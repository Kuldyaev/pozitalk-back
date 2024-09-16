<?php

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthCheckCodeRequest;
use App\Models\Auth\AuthProviderStatusEnum;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class AuthValidateCodeController extends AuthController
{
    #[Post(
        tags: ['Authentification'],
        path: '/auth/{AuthProviderType}/validate-code',
        summary: 'Check code for authentication',
        description: 'Валидация кода отправленного в сервис авторизации (использует временный access token)',
        parameters: [
            new Parameter(
                name: 'AuthProviderType',
                in: 'path',
                example: 'email',
                description: 'Провайдер авторизации (email)',
            )
        ],
        requestBody: new RequestBody(
            content: new JsonContent(
                ref: '#/components/schemas/AuthValidateCodeRequest',
            ),
        ),
        responses: [
            new Response(response: 200, description: 'OK', content: new JsonContent()),
            new Response(response: 403, description: 'Forbiden', content: new JsonContent()),
            new Response(response: 401, description: 'Unauthorized', content: new JsonContent()),
        ]
    )]
    public function __invoke(AuthCheckCodeRequest $request, string $provider): JsonResponse
    {
        $request->authProvider->update([
            'status' => AuthProviderStatusEnum::REGISTERED->value
        ]);

        return $this->setCookies(
            new JsonResponse(),
            app(AuthCookieFromAuthProviderAction::class)->run($request->authProvider)
        );
    }
}
