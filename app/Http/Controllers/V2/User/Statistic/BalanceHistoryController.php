<?php

namespace App\Http\Controllers\V2\User\Statistic;

use Vi\Actions\User\Statistic\UserLoadBalanceHistoryAction;
use App\Http\Controllers\V2\Controller;
use App\Http\Requests\User\Statistic\BalanceHistoryRequest;
use App\Http\Resources\User\Statistic\BalanceHistoryResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class BalanceHistoryController extends Controller
{
    #[Get(
        tags: ['User.Statistic'],
        operationId: 'UserBalanceHistory',
        path: '/user/{user}/statistic/balance-history',
        description: 'История баланса пользователя',
        parameters: [
            new Parameter(
                name: 'user',
                in: 'path',
                description: 'User ID',
            ),
            new Parameter(
                name: 'from',
                in: 'query',
                description: 'Смещение (pagination.next_from)',
            ),
            new Parameter(
                name: 'limit',
                in: 'query',
                description: 'Лимит',
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
                                ref: '#/components/schemas/BalanceHistoryResource'
                            )
                        ),
                        new Property(
                            property: 'pagination',
                            type: 'object',
                            properties: [
                                new Property(
                                    property: 'next_from',
                                    type: 'number',
                                    example: 123,
                                ),
                                new Property(
                                    property: 'count',
                                    type: 'number',
                                    example: 123,
                                ),
                            ]
                        )

                    ]
                )
            )
        ]
    )]
    public function __invoke(BalanceHistoryRequest $request, User $user)
    {
        $history = app(UserLoadBalanceHistoryAction::class)->run(
            $user,
            $request->input('from'),
            $request->input('limit'),
        );

        return tap(
            BalanceHistoryResource::collection($history),
            fn(AnonymousResourceCollection $res) => $res->additional([
                'pagination' => [
                    'next_from' => $history->last()?->id,
                    'count' => $res->count(),
                ]
            ])
        );
    }
}
