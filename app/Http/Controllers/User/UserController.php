<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ReportReferral;
use App\Models\Seling;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class UserController extends Controller
{
    #[Get(
        path: '/user/me',
        operationId: 'getMe',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(
                    ref: '#/components/schemas/User',
                    type: 'object',
                )
            )
        ]
    )]
    public function me(Request $request): UserResource
    {
        $user = auth()->user();
        
        return new UserResource($user);
    }

}
