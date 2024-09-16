<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Http\Requests\ArbChangeRequest;
use App\Http\Requests\ArbHistoryRequest;
use App\Http\Requests\ArbReopenRequest;
use App\Http\Requests\IaSystemChangeRequest;
use App\Models\ArbDeposit;
use App\Models\IaSystemDeposit;
use App\Models\ReportReferral;
use App\Models\User;
use App\Services\ArbService;
use App\Services\IaSystemService;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\Response;

class IaSystemController extends Controller
{
    #[Get(
        path: "/ia-system/pools",
        description: "Долевые пулы ia-system",
        tags: ["IA-System"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function pools(IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Долевые пулы ia-system',
            'data' => $service->iaSystemPools()
        ]);
    }

    #[Get(
        path: "/ia-system/history",
        description: "История депозитов ia-system. Статусы: 1 - ожидание, 2 - активен, 3 - закончился, 4 - выведен.",
        tags: ["IA-System"],
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
    public function history(ArbHistoryRequest $request, IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'История депозитов ia-system.',
            'data' => $service->history($request)
        ]);
    }

    #[Put(
        path: "/ia-system/reopen",
        description: "Продлить депозит ia-system",
        tags: ["IA-System"],
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
    public function reopen(ArbReopenRequest $request, IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Успешно!',
            'data' => $service->reopen($request)
        ]);
    }

    #[Put(
        path: "/ia-system/change",
        description: "Изменить депозит ia-system",
        tags: ["IA-System"],
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
    public function change(IaSystemChangeRequest $request, IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Успешно!',
            'data' => $service->change($request)
        ]);
    }

    #[Get(
        path: "/ia-system/statistic",
        description: "Статистика по личным депозитам ia-system",
        tags: ["IA-System"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function statistic(IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Статистика по личным депозитам ia-system.',
            'data' => $service->statistic()
        ]);
    }

    #[Get(
        path: "/ia-system/calculation-pools",
        description: "Подсчет для долевых пулов ia-system",
        tags: ["IA-System"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function calculationPools(IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Подсчет для долевых пулов ia-system.',
            'data' => $service->calculationPools()
        ]);
    }

    #[Get(
        path: "/ia-system/",
        description: "Баланс ia-system",
        tags: ["IA-System"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function index(IaSystemService $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Баланс ia-system пользователя.',
            'data' => $service->index()
        ]);
    }


    public function allDeposits()
    {
        $response = [];

        $deps = IaSystemDeposit::orderBy('id', 'desc')->paginate(10);
        foreach ($deps as $dep) {
            $dep['user'] = User::find($dep->user_id);
        }
        $response['deposits'] = $deps;

        $response['short-info'] = [
            'all_sum' => IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount'),
            'count' => IaSystemDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->count(),
        ];

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function wontRequestDeposits()
    {
        $deps = IaSystemDeposit::where('is_wont_request', true)->orderBy('updated_at', 'desc')->paginate(10);
        foreach ($deps as $dep) {
            $dep['user'] = User::find($dep->user_id);
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $deps
        );
    }

    public function start(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $arbDeposit = IaSystemDeposit::findOrFail($request->get('id'));

        if ($arbDeposit->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Уже активен депозит'
            ]);
        }

        $arbDeposit->is_active = true;
        $arbDeposit->start = Carbon::now();
        $arbDeposit->save();

        return response()->json([
            'success' => true,
            'message' => 'Депозит активирован',
            'data' => $arbDeposit
        ]);
    }

    public function close(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $arbDeposit = IaSystemDeposit::findOrFail($request->get('id'));

        if (!$arbDeposit->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Депозит не активен'
            ]);
        }

        $arbDeposit->is_active = false;
        $arbDeposit->is_wont_request = null;
        $arbDeposit->save();

        $user = User::findOrFail($arbDeposit->user_id);
        $user->wallet += $arbDeposit->amount;
        $user->save();

        $rep = ReportReferral::create([
            'owner_id' => 1,
            'member_id' => $user->id,
            'sum' => $arbDeposit->amount,
            'type' => 'ia_system_request',
            'data' => [
                'balance' => $user->wallet,

            ]
        ]);
        $rep->type = 'ia_system_request';
        $rep->save();

        return response()->json([
            'success' => true,
            'message' => 'Депозит закрыт',
            'data' => $arbDeposit
        ]);
    }
}
