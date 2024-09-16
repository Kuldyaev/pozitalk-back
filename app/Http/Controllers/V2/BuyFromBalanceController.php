<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Models\ArbBalance;
use App\Models\ArbDeposit;
use App\Models\IaSystem;
use App\Models\IaSystemDeposit;
use App\Services\BuyFromBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;

class BuyFromBalanceController extends Controller
{
    #[Post(
        path: "/buy/balance",
        description: "Покупка с помощью баланса.\n
            product:\n
            - ia_system_deposit : создание депозита ARB (доп параметры - amount, count_months)
        ",
        tags: ["Buy"],
        parameters: [
            new Parameter(
                name: 'product',
                in: 'query',
                example: 'ia_system_deposit',
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
    public function index(Request $request, BuyFromBalanceService $service): JsonResponse
    {
        $request->validate(['product' => 'required']);
        $product = $request->get('product');
        $user = auth()->user();

        if($product == 'ia_system_deposit') {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'count_months' => 'required|numeric|in:12,18',
            ]);

            $balance = IaSystem::where('user_id', $user->id)->first();

            if($user->wallet < $request->get('amount')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $response = $service->arb_pay($user, $request->get('count_months'), $request->get('amount'));
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'product указан не верно.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Покупка с помощью тикетов.',
            'data' => $response
        ]);
    }
}
