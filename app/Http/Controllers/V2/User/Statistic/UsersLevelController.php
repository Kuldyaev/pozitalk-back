<?php

declare(strict_types=1);

namespace App\Http\Controllers\V2\User\Statistic;

use Vi\Actions\User\Statistic\UserLoadInnerUsersLevelAction;
use App\Http\Controllers\V2\Controller;
use App\Http\Resources\User\Statistic\UserStatisticUsersLevelResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class UsersLevelController extends Controller
{
    #[Get(
        tags: ['User.Statistic'],
        operationId: 'UserStatisticUsersLevel',
        path: '/user/{user}/statistic/users-first-level',
        description: 'Статистика по пользователям',
        parameters: [
            new Parameter(
                name: 'user',
                in: 'path',
                description: 'User id',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(
                    properties: [
                        new Property(
                            property: 'data',
                            type: 'array',
                            items: new Items(
                                ref: '#/components/schemas/UserStatisticUsersLevelResource'
                            )
                        ),
                        new Property(
                            property: 'withHierarchyCount',
                            type: 'integer'
                        ),
                        new Property(
                            property: 'count',
                            type: 'integer'
                        )
                    ],
                )
            )
        ]
    )]
    public function __invoke(
        Request $request,
        User $user,
        UserLoadInnerUsersLevelAction $userLoadInnerUsersLevelAction
    ): JsonResource {
        $users = $userLoadInnerUsersLevelAction->run($user);

        return (UserStatisticUsersLevelResource::collection($users))
            ->additional([
                'withHierarchyCount' => $users->sum(
                    fn(User $user) => $user->organization_count > 0 ? 1 : 0
                ),
                'count' => $users->count(),
            ]);
    }
}
