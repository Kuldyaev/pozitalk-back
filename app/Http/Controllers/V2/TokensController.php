<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Models\TokenPrivateReport;
use App\Models\TokenStackingReport;
use App\Models\TokenVestingReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;

class TokensController extends Controller
{
    CONST PRIVATE_EXCHANGE_RATE = 0.46;
    CONST STAKING_EXCHANGE_RATE = 0.23;
    CONST VESTING_EXCHANGE_RATE = 0.23;

    #[Get(
        path: "/tokens/",
        description: "Токеномика",
        tags: ["Tokenomiks"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $user = auth()->user();

        $response = [
            'private' => [
                'title' => 'Private',
                'tokens' => $user->token_private,
                'usdt' => $user->token_private * self::PRIVATE_EXCHANGE_RATE,
                'exchange_rate' => self::PRIVATE_EXCHANGE_RATE,
                'available_for_withdrawal' => 0,
                'count_shares' => $user->token_private / 1000 ?? 0,
            ],
            'staking' => [
                'title' => 'Staking',
                'tokens' => $user->token_stacking,
                'usdt' => $user->token_stacking * self::STAKING_EXCHANGE_RATE,
                'exchange_rate' => self::STAKING_EXCHANGE_RATE,
                'available_for_withdrawal' => 0,
            ],
            'vesting' => [
                'title' => 'Vesting',
                'tokens' => $user->token_vesting,
                'usdt' => $user->token_vesting * self::VESTING_EXCHANGE_RATE,
                'exchange_rate' => self::VESTING_EXCHANGE_RATE,
                'available_for_withdrawal' => 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Курс.',
            'data' => $response
        ]);
    }

    #[Get(
        path: "/tokens/history",
        description: "История токенов пользователя",
        tags: ["Tokenomiks"],
        parameters: [
            new Parameter(
                name: "field",
                description: "Поле для сортировки (count, type, usdt, created_at)",
                in: "query",
                required: false
            ),
            new Parameter(
                name: "order",
                description: "Порядок сортировки (asc, desc)",
                in: "query",
                required: false
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: "Успешный запрос",
                content: new JsonContent()
            )
        ]
    )]
    public function history(Request $request): JsonResponse
    {
        $request->validate([
            'field' => 'nullable|string|in:count,type,usdt,created_at',
            'order' => 'nullable|string|in:asc,desc'
        ]);

        $field = $request->get('field') ?? 'id';
        $order = $request->get('order') ?? 'desc';

        $user_id = auth()->user()->id;

        $tokenVestingTable = (new TokenVestingReport())->getTable();
        $tokenStackingTable = (new TokenStackingReport())->getTable();
        $tokenPrivateTable = (new TokenPrivateReport())->getTable();

        $tokenVestingQuery = DB::table($tokenVestingTable)
            ->select('user_id', 'count', 'type', 'usdt', 'balance', 'created_at')
            ->addSelect(DB::raw("'vesting' as token"))
            ->where('user_id', $user_id)
            ->orderBy($field, $order);

        $tokenStackingQuery = DB::table($tokenStackingTable)
            ->select('user_id', 'count', 'type', 'usdt', 'balance', 'created_at')
            ->addSelect(DB::raw("'staking' as token"))
            ->where('user_id', $user_id)
            ->orderBy($field, $order);

        $tokenPrivateQuery = DB::table($tokenPrivateTable)
            ->select('user_id', 'count', 'type', 'usdt', 'balance', 'created_at')
            ->addSelect(DB::raw("'private' as token"))
            ->where('user_id', $user_id)
            ->orderBy($field, $order);

        $result = $tokenVestingQuery->union($tokenStackingQuery)->union($tokenPrivateQuery)->get();

        foreach ($result as $item) {
            $item->date = Carbon::parse($item->created_at)->unix();
        }

        return response()->json([
            'success' => true,
            'message' => 'История токенов пользователя.',
            'data' => $result
        ]);
    }
}
