<?php

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Actions\Auth\AuthRegistrationWithEmailAction;
use App\DTO\Auth\AuthRegistrationWithEmailDTO;
use App\Http\Requests\Auth\AuthRegistrationRequest;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

class AuthRegistrationController extends AuthController
{

    #[Post(
        tags: ["Authentification"],
        path: "/auth/{AuthProviderType}/registration",
        summary: "User registration",
        description: "Возвращает статус провайдера и временные токены аутентификации",
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
                anyOf: [
                    new Schema(ref: '#/components/schemas/AuthRegistrationEmailRequest'),
                ]
            )
        ),
        responses: [
            new Response(response: 200, description: 'OK', content: new JsonContent()),
            new Response(
                response: 422,
                description: 'Unprocessable Content',
                content: new JsonContent(properties: [
                    new Property(property: 'message', type: 'string', example: 'some field is invalid'),
                    new Property(property: 'errors', type: 'array', items: new Items(type: 'string')),
                ]),
            )
        ]
    )]
    public function __invoke(AuthRegistrationRequest $request, string $provider): JsonResponse
    {
        $authProvider = match ($provider) {
            'email' => app(AuthRegistrationWithEmailAction::class)
                ->run(AuthRegistrationWithEmailDTO::makeFromRequest($request)),
        };

        return $this->setCookies(
            new JsonResponse(),
            app(AuthCookieFromAuthProviderAction::class)->run($authProvider)
        );
    }
}
