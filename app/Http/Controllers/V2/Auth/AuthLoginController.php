<?php

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieFromAuthProviderAction;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthLoginController extends AuthController
{
    #[Post(
        tags: ["Authentification"],
        path: "/auth/{AuthProviderType}/login",
        summary: "User login",
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
                    new Schema(ref: '#/components/schemas/AuthLoginEmailRequest')
                ]
            ),
        ),
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            ),
            new Response(
                response: 403,
                description: 'Forbidden',
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
    public function __invoke(AuthLoginRequest $request, AuthProviderEnum|string $provider): JsonResource|JsonResponse
    {
        $user = User::where('email', $request->str('email'))
            ->first();

        /** @var AuthProvider $authProvider */
        $authProvider = $user->authProviders()
            ->where('provider', $provider)
            ->first();

        if (is_null($authProvider)) {
            $authProvider = $user->authProviders()->create([
                'provider' => $provider,
                'status' => AuthProviderStatusEnum::REGISTERED->value,
                'data' => ['old_user' => true]
            ]);
        }

        if (
            is_null($user) || !Hash::check($request->str('password'), $user->password)
        ) {
            return throw new UnprocessableEntityHttpException(__('auth.incorrect_email_or_password'));
        }

        if ($authProvider->status !== AuthProviderStatusEnum::REGISTERED) {
            return new JsonResponse(
                ['message' => __('auth.unvalidated_auth_provider')],
                403
            );
        }

        return $this->setCookies(
            new JsonResponse(),
            app(AuthCookieFromAuthProviderAction::class)->run($authProvider)
        );
    }
}
