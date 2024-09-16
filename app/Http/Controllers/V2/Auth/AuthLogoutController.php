<?php

namespace App\Http\Controllers\V2\Auth;

use App\Http\Controllers\Controller;
use Cookie;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response as FacadesResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class AuthLogoutController extends AuthController
{
    #[Get(
        tags: ['Authentification'],
        path: '/auth/logout',
        summary: 'Выход из системы',
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent()
            )
        ]
    )]
    public function __invoke(Request $request): HttpResponse
    {
        return response('', HttpResponse::HTTP_NO_CONTENT)
            ->withCookie(cookie('access_token', null, 0, secure: true, sameSite: 'none'))
            ->withCookie(cookie('refresh_token', null, 0, secure: true, sameSite: 'none'));

    }
}
