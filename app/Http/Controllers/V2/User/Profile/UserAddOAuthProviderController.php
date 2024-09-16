<?php

namespace App\Http\Controllers\V2\User\Profile;

use App\Actions\Auth\AuthTelegram\AuthOAuthAddTelegramAction;
use App\Actions\Auth\AuthTelegram\AuthOAuthAddTelegramCreateLinkAction;
use App\Http\Controllers\V2\Controller;
use App\Http\Resources\UserResource;
use App\Models\Auth\AuthProviderEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Stringable;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class UserAddOAuthProviderController extends Controller
{
    #[Post(
        tags: ['User.Authentification'],
        operationId: 'AddOAuthProvider',
        path: '/user/profile-info/oauth/{provider}/init',
        summary: 'Initiate OAuth provider authorization process',
        description: 'Инициирование процесса авторизации через социальную сеть',
        parameters: [
            new Parameter(
                name: 'provider',
                in: 'path',
                example: 'telegram',
                description: 'Тип провайдера авторизации (telegram)'
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
                                new Property(property: 'status', ref: '#/components/schemas/AuthProviderStatusEnum'),
                                new Property(property: 'botId', type: 'number')
                            ]
                        )
                    ]
                )
            ),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(response: 403, description: 'Forbidden'),
            new Response(response: 500, description: 'Internal Server Error')
        ]
    )]
    public function init(Request $request, AuthProviderEnum $provider): JsonResponse
    {
        $isCustomDomain = env('FEATURE_AUTHBOT_CAST')
            ? (new Stringable($request->header('Origin')))
                ->contains(env('FEATURE_AUTHBOT_CAST_DOMAIN'))
            : false;

        $authProvider = match ($provider) {
            AuthProviderEnum::TELEGRAM => app(AuthOAuthAddTelegramCreateLinkAction::class)
                ->run($request->user(), $isCustomDomain),
            default => throw new \Exception('Unsupported provider'),
        };

        return new JsonResponse([
            'data' => [
                'status' => $authProvider->status
            ] + $authProvider->data
        ]);
    }

    #[Post(
        tags: ['User.Authentification'],
        operationId: 'AddOAuthProviderCallback',
        path: '/user/profile-info/oauth/{provider}/callback',
        summary: 'Callback from OAuth provider',
        description: 'Обработка callback от социальной сети',
        parameters: [
            new Parameter(
                name: 'provider',
                in: 'path',
                example: 'telegram',
                description: 'Тип провайдера авторизации (telegram)'
            )
        ],
        requestBody: new RequestBody(
            content: new JsonContent(ref: '#/components/schemas/AuthOAuthCallbackRequest'),
        ),
        responses: [
            new Response(response: 200, description: 'OK', content: new JsonContent(properties: [
                new Property(
                    property: 'data',
                    type: 'object',
                    ref: '#/components/schemas/User'
                )
            ])),
            new Response(response: 401, description: 'Unauthorized'),
            new Response(response: 403, description: 'Forbidden')
        ],
    )]
    public function callback(Request $request, AuthProviderEnum $provider): UserResource
    {
        $request->validate(['data' => 'required']);

        $isCastDomain = env('FEATURE_AUTHBOT_CAST')
            ? (new Stringable($request->header('Origin')))
                ->contains(env('FEATURE_AUTHBOT_CAST_DOMAIN'))
            : false;

        $authProvider = match ($provider) {
            AuthProviderEnum::TELEGRAM => app(AuthOAuthAddTelegramAction::class)
                ->run($request->user(), $request->input('data'), $isCastDomain)
        };

        return new UserResource($request->user()->refresh());
    }
}
