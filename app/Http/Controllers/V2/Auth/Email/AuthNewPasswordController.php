<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\Auth\Email;

use App\Http\Controllers\V2\Controller;
use App\Http\Requests\Auth\Email\AuthNewPasswordRequest;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class AuthNewPasswordController extends Controller
{
    #[Patch(
        tags: ['User.Authentification'],
        operationId: 'AuthNewPassword',
        path: '/auth/new-password',
        summary: 'Update user password',
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new Property(
                        property: 'password',
                        type: 'string',
                        description: 'Current password'
                    ),
                    new Property(
                        property: 'password_new',
                        type: 'string',
                        description: 'new password'
                    ),
                    new Property(
                        property: 'password_new_confirmation',
                        type: 'string',
                        description: 'new password confirmation'
                    )
                ]
            )
        ),
        responses: [
            new Response(
                response: 204,
                description: 'OK',
            ),
        ]
    )]
    public function __invoke(AuthNewPasswordRequest $request): void
    {
        
        /**
         * @var \App\Models\User $user
         */
        $user = $request->user();
        $user->password = Hash::make($request->input('password_new'));
        $user->save();
    }
}
