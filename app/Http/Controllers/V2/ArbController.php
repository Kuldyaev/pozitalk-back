<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Http\Requests\ArbChangeRequest;
use App\Http\Requests\ArbHistoryRequest;
use App\Http\Requests\ArbReopenRequest;
use App\Services\ArbService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\Response;

class ArbController extends Controller
{
    #[Get(
        path: "/arb/pools",
        description: "Долевые пулы ARB",
        tags: ["ARB"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function arbPools(ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Долевые пулы ARB',
            'data' => $service->arbPools()
        ]);
    }

    #[Get(
        path: "/arb/history",
        description: "История депозитов ARB. Статусы: 1 - ожидание, 2 - активен, 3 - закончился, 4 - выведен.",
        tags: ["ARB"],
        parameters: [
            new Parameter(
                name: 'field',
                in: 'query',
                example: 'created_at,status,count_months,amount,percent',
            ),
            new Parameter(
                name: 'order',
                in: 'query',
                example: 'asc,desc',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function history(ArbHistoryRequest $request, ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'История депозитов ARB.',
            'data' => $service->history($request)
        ]);
    }

    #[Put(
        path: "/arb/reopen",
        description: "Продлить депозит ARB",
        tags: ["ARB"],
        parameters: [
            new Parameter(
                name: 'id',
                in: 'query',
                example: '123',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function reopen(ArbReopenRequest $request, ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Успешно!',
            'data' => $service->reopen($request)
        ]);
    }

    #[Put(
        path: "/arb/change",
        description: "Изменить депозит ARB",
        tags: ["ARB"],
        parameters: [
            new Parameter(
                name: 'id',
                in: 'query',
                example: '123',
            ),
            new Parameter(
                name: 'count_months',
                in: 'query',
                example: '6|12|18',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function change(ArbChangeRequest $request, ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Успешно!',
            'data' => $service->change($request)
        ]);
    }

    #[Get(
        path: "/arb/statistic",
        description: "Статистика по личным депозитам ARB",
        tags: ["ARB"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function statistic(ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Статистика по личным депозитам ARB.',
            'data' => $service->statistic()
        ]);
    }

    #[Get(
        path: "/arb/calculation-pools",
        description: "Подсчет для долевых пулов ARB",
        tags: ["ARB"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function calculationPools(ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Подсчет для долевых пулов ARB.',
            'data' => $service->calculationPools()
        ]);
    }

    #[Get(
        path: "/arb/",
        description: "Лимит пользователя и информация о ARB",
        tags: ["ARB"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function index(ArbService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Лимит пользователя и информация о ARB.',
            'data' => $service->index()
        ]);
    }
}
