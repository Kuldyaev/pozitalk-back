<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Models\UsdtWallet;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Response;

class BuyFromUsdtController extends Controller
{
    #[Get(
        path: "/buy/usdt",
        description: "Получить QR код для оплаты.\n
            product:\n
            - account : тикеты\n
            - bronze : статус за 200$\n
            - silver : статус за 800$\n
            - gold : статус за 2400$\n
            - platinum : статус за 5000$\n
            - token_private : приватные токены (доли)\n
            - balance_plus : пополнение баланса\n
            - life_* : стартовые пакеты (*: 1-6)\n"
        ,
        tags: ["Buy"],
        parameters: [
            new Parameter(
                name: 'product',
                in: 'query',
                example: 'account',
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
    public function walletGet(Request $request): JsonResponse
    {
        $request->validate([
            'product' => ['string', 'required'],
        ]);
        $user = User::find(Auth::user()->id);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'wallet' => $user->getUsdtWallet($request->get('product')),
                'wallet_qr' => $user->getUsdtWalletQr($request->get('product')),
            ]
        );
    }

    #[Post(
        path: "/buy/usdt",
        description: "Для кнопки подтверждения оплаты.\n
            Метод не обязательный. Нужен для проверки оплаты здесь и сейчас\n
            product:\n
            - account : тикеты\n
            - bronze : статус за 200$\n
            - silver : статус за 800$\n
            - gold : статус за 2400$\n
            - platinum : статус за 5000$\n
            - token_private : приватные токены (доли)\n
            - balance_plus : пополнение баланса\n
            - life_* : стартовые пакеты (*: 1-6)\n"
        ,
        tags: ["Buy"],
        parameters: [
            new Parameter(
                name: 'product',
                in: 'query',
                example: 'account',
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
    public function walletPost(Request $request): JsonResponse
    {
        $request->validate([
            'product' => ['string', 'required'],
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                UsdtWallet::checkWallet(Auth::user()->id, $request->get('product'))
            ]
        );
    }
}
