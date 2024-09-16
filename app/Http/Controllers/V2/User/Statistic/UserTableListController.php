<?php

namespace App\Http\Controllers\V2\User\Statistic;

use Vi\Actions\User\Statistic\UserLoadInnerUsersAction;
use Vi\Actions\User\Statistic\UserLoadInnerUsersListAction;
use App\Http\Controllers\V2\Controller;
use App\Http\Requests\User\Statistic\UserStatisticUserListRequest;
use App\Http\Resources\User\Statistic\UserStatisticUsersListResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class UserTableListController extends Controller
{
    #[Get(
        tags: ['User.Statistic'],
        operationId: 'UserStatisticUserList',
        path: '/user/{user}/statistic/users-list',
        description: 'Список пользователей. Кэш обновляется на offset=0',
        parameters: [
            new Parameter(
                name: 'user',
                in: 'path',
                description: 'User ID',
            ),
            new Parameter(
                name: 'offset',
                in: 'query',
                description: 'Смещение (Кол-во загруженных записей (next_offset))',
            ),
            new Parameter(
                name: 'limit',
                in: 'query',
                description: 'Лимит',
            ),
            new Parameter(
                name: 'sort_by',
                in: 'query',
                description: 'Поле для сортировки (line,status,first_line_count,organization_count,organization_total_sail_sum,organization_total_sail_sum)',
            ),
            new Parameter(
                name: 'sort_reverse',
                in: 'query',
                description: 'Обратная сортировка (DECS) true/false',
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
                                ref: '#/components/schemas/UserStatisticUsersListResource'
                            )
                        ),
                        new Property(
                            property: 'pagination',
                            type: 'object',
                            properties: [
                                new Property(
                                    property: 'all_count',
                                    type: 'integer'
                                ),
                                new Property(
                                    property: 'count',
                                    type: 'integer'
                                ),
                                new Property(
                                    property: 'next_offset',
                                    type: 'integer'
                                )
                            ]
                        )
                    ],
                )
            )
        ]
    )]
    public function __invoke(
        UserStatisticUserListRequest $request,
        User $user,
        UserLoadInnerUsersListAction $userLoadInnerUsersListAction
    ): JsonResource {
        $users = $userLoadInnerUsersListAction->run(
            $user,
            $request->integer('offset') === 0,
            orderBy: $request->input('sort_by', null),
            orderDescending: $request->boolean('sort_reverse', false)
        );

        return tap(
            UserStatisticUsersListResource::collection(
                $users->slice($request->integer('offset'), $request->integer('limit'))
            ),
            fn(AnonymousResourceCollection $res) => $res->additional([
                'sort' => [
                    'access' => [
                        'line',
                        'status',
                        'first_line_count',
                        'organization_count',
                        'organization_total_sail_sum',
                        'organization_total_sail_sum'
                    ],
                    'sorted' => (bool) $request->input('sort_by', null),
                    'by' => $request->input('sort_by', null),
                    'reverse' => $request->boolean('sort_reverse', false),
                ],
                'pagination' => [
                    'all_count' => $users->count(),
                    'count' => $res->count(),
                    'next_offset' => $res->count() + $request->integer('offset'),
                ]
            ])
        );
    }
}
