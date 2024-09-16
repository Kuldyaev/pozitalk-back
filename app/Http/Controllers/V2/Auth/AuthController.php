<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth;

use App\Actions\Auth\AuthCookie\AuthCookieActionTrait;
use App\Http\Controllers\V2\Controller;
use App\Http\Requests\Auth\AuthLoginAsRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use Queue;

class AuthController extends Controller
{
    use AuthCookieActionTrait;

    #[Delete(
        tags: ["Special"],
        path: "/auth/drop-me",
        parameters: [
            new Parameter(
                name: 'code',
                in: 'query'
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent()
            )
        ]
    )]
    public function dropMe(Request $request)
    {
        return $request->user()->delete();
    }

    #[Delete(
        tags: ["Special"],
        path: "/auth/drop-by-email",
        parameters: [
            new Parameter(
                name: 'email',
                in: 'query',
            ),
            new Parameter(
                name: 'code',
                in: 'query',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent()
            )
        ]
    )]
    /**
     * @unsafe
     */
    public function dropByEmail(Request $request)
    {
        User::where('email', $request->input('email'))->delete();
    }

    #[Get(
        tags: ['Special'],
        path: '/auth/login-as',
        parameters: [
            new Parameter(
                name: 'user_id',
                in: 'query',
            ),
            new Parameter(
                name: 'code',
                in: 'query',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent()
            )
        ]
    )]
    public function loginAs(AuthLoginAsRequest $request)
    {
        $user = User::find($request->input('user_id'));

        return $this->setCookies(
            new JsonResponse(),
            [
                'access' => $this->createCookie($user, 'access_token', $this->getUserAbilities($user), 'access_token'),
                'refresh' => $this->createCookie($user, 'refresh_token', ['auth:refresh-token', 'refresh_token'])
            ]
        );
    }
}
