<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V2\Controller;
use App\Http\Requests\IndexToken\StatisticRequest;
use App\Models\IaSystem;
use App\Models\IaSystemReport;
use App\Models\IndexAutoPurchase;
use App\Models\IndexDeposit;
use App\Models\ReportReferral;
use App\Models\TicketReport;
use App\Services\IndexToken;
use App\Services\Response\ResponseService;
use App\Services\Web3Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

class IndexController extends Controller
{
    #[Get(
        path: "/index-token/statistic",
        description: "Статистика токена index",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'time_interval',
                in: 'query',
                example: 'all|week|month',
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
    public function statistic(StatisticRequest $request, IndexToken $indexService): JsonResponse
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexService->statistic($request->get('time_interval', 'all'))
        );
    }

    #[Get(
        path: "/index-token/price",
        description: "Стоимость токена index",
        tags: ["Index Token"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function price(): JsonResponse
    {

        Artisan::call('index:cron');
        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();

        $response = [
            'index_token_price' => $indexLast->index,
            'date_time' => Carbon::now(),
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    #[Get(
        path: "/index-token/history",
        description: "История покупок/продаж VBTI",
        tags: ["Index Token"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function history(IndexToken $indexService): JsonResponse
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexService->history()
        );
    }

    #[Get(
        path: "/index-token/structure",
        description: "Состав индекса",
        tags: ["Index Token"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function structure(IndexToken $indexService): JsonResponse
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexService->structure()
        );
    }

    #[Get(
        path: "/index-token/auto-pay",
        description: "Информация по программе автопокупки индекса",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'code',
                in: 'query',
                example: '20',
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
    public function autoInfo(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        $user = Auth::user();
        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();
        $indexAutoQuery = IndexAutoPurchase::where('user_id', $user->id)->where('program_id', $request->get('code'))->first();

        $sumLimit = TicketReport::where('user_id', Auth::user()->id)
            ->where('type', 'index_pay_' . $request->get('code'))
            ->sum('comment');
        $sumPay = IndexDeposit::where('user_id', Auth::user()->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->sum('amount');

        $response = [
            'program_id' => $indexAutoQuery->program_id,
            'error_code' => $indexAutoQuery->error_code,
            'limit' => $sumLimit - $sumPay,
            'regularity' => $indexAutoQuery->regularity,
            'week_usdt' => $indexAutoQuery->amount,
            'week_vbti' => $indexAutoQuery->amount * $indexLast->index,
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    #[Get(
        path: "/index-token/",
        description: "Ваши программы VBTI",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'code',
                in: 'query',
                example: '20',
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
    public function indexInfo(Request $request, IndexToken $indexService)
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexService->indexInfo($request->get('code'))
        );
    }

    #[Get(
        path: "/index-token/check-limit",
        description: "Проверка лимита VBTI",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'code',
                in: 'query',
                example: '20',
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
    public function checkLimitProgram(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|integer',
        ]);

        $sumLimit = TicketReport::where('user_id', Auth::user()->id)
            ->where('type', 'index_pay_' . $request->get('code'))
            ->sum('comment');

        $sumPay = IndexDeposit::where('user_id', Auth::user()->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->sum('amount');

        $response = [
            'limit' => $sumLimit - $sumPay
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    #[Post(
        path: "/index-token/buy",
        description: "Покупка токена index",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'amount',
                in: 'query',
                example: 'сумма покупки',
            ),
            new Parameter(
                name: 'wallet_address',
                in: 'query',
                example: 'адрес кошелька',
            ),
            new Parameter(
                name: 'type',
                in: 'query',
                example: 'wallet|balance',
            ),
            new Parameter(
                name: 'code',
                in: 'query',
                example: 'программа',
            ),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function buyVTI(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'wallet_address' => 'required|string',
            'type' => 'required|string',
            'code' => 'required|integer',
        ]);

        $return = $this->buyNow($request);

        if(isset($return)){
            return $return;
        }else{
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                []
            );
        }
    }

    #[Put(
        path: "/index-token/1",
        description: "Покупка токена index",
        tags: ["Index Token"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function stopAutoPurchase(int $id, Request $request){
        $user = Auth::user();

        $purchase = IndexAutoPurchase::where('user_id', $user->id)->where('program_id', $id)->orderBy('created_at', 'desc')->first();
        if ($purchase) {
            // Найден purchase, обновим поле is_active на false
            $purchase->update([
                'is_active' => false,
                'regularity' => null,
                'error_code' => null,
                'amount' => 0
            ]);

            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                ['Auto purchase stopped successfully.']
            );
        } else {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['message' => 'Auto purchase not found.'],
                []
            );
        }
    }

    #[Post(
        path: "/index-token/auto-pay",
        description: "Создание автопокупки токена index",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'code',
                in: 'query',
                example: 'программа',
            ),
            new Parameter(
                name: 'amount',
                in: 'query',
                example: 'сумма покупки',
            ),
            new Parameter(
                name: 'regularity',
                in: 'query',
                example: 'week|month',
            ),
            new Parameter(
                name: 'pay_now',
                in: 'query',
                example: 'bool',
            ),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function autoPurchase(Request $request)
    {
        $request->validate([
            'code' => 'required|integer',
            'amount' => 'required|numeric',
            'regularity' => 'required|string|in:week,month',
            'pay_now' => 'nullable|boolean'
        ]);

        $user = Auth::user();

        // Генерируем уникальный ключ для блокировки
        $lockKey = 'autoPurchaseLock_' . $user->id;

        // Проверяем, заблокирован ли метод
        if (Cache::has($lockKey)) {
            return ResponseService::sendJsonResponse(
                false,
                429, // Код ответа 429 - Слишком много запросов
                ['message' => 'Auto purchase is already in progress.'],
                []
            );
        }

        // Устанавливаем блокировку на 5 минут (или любое другое подходящее время)
        Cache::put($lockKey, true, now()->addMinutes(5));

        $lastWallet = $user->cryptoWallets()->where('is_active', true)->latest()->first();

        // Проверяем, есть ли у пользователя кошелек
        if (!isset($lastWallet)){
            return ResponseService::sendJsonResponse(
                false,
                404,
                [],
                ['message' => 'User does not have any wallets.']
            );
        }

        $request->merge(['wallet_address' => $lastWallet->wallet_address]);

        //если сейчас хотим оплатить
        if($request->get('pay_now')) {
            $result = $this->buyVTIfromHandler($request, true);

            if ($result['status'] === false) {
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message' => $result['message']],
                    []
                );
            }
        }

        $indexAutoQuery = IndexAutoPurchase::where('user_id', $user->id)->where('program_id', $request->get('code'));
        $indexAuto = $indexAutoQuery->first();
        if(!isset($indexAuto)){
            IndexAutoPurchase::create([
                'user_id' => $user->id,
                'program_id' => $request->get('code'),
                'amount' => $request->get('amount'),
                'regularity' => $request->get('regularity'),
                'wallet_id' => $lastWallet->id
            ]);
        }else{
            $indexAutoQuery->update([
                'amount' => $request->get('amount'),
                'wallet_id' => $indexAuto->wallet_id,
                'error_code' => null,
                'regularity' => $request->get('regularity'),
                'is_active' => true
            ]);
        }

        // В конце метода удаляем блокировку
        Cache::forget($lockKey);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Success.']
        );
    }

    #[Post(
        path: "/index-token/sell",
        description: "Покупка токена index",
        tags: ["Index Token"],
        parameters: [
            new Parameter(
                name: 'amount',
                in: 'query',
                example: 'сумма покупки',
            ),
            new Parameter(
                name: 'wallet_address',
                in: 'query',
                example: 'адрес кошелька',
            ),
            new Parameter(
                name: 'type',
                in: 'query',
                example: 'wallet|balance',
            ),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function sellVTI(Request $request) {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'wallet_address' => 'required|string',
        ]);

        $user = Auth::user();

        // берем текущий курс
        Artisan::call('index:cron');
        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();
        $secondIndex = IndexToken::orderBy('id', 'desc')->skip(1)->take(1)->first();

        // Вычисляем порог для разницы в 5%
        $threshold = $secondIndex->index * 0.05;
        if ((abs($indexLast->index - $secondIndex->index) > $threshold) || $indexLast->index == 0) {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['message'=>'Неккоректный курс'],
                []
            );
        }

        $VTIAmount = $request->get('amount');
        $VTIPrice = $indexLast->index;
        $userAddress = $request->get('wallet_address');
        $timeStamp = Carbon::now()->timestamp;
        $type = $request->get('type');

        if($type == 'wallet') {
            //вызываешь контракт
            $response = Web3Service::sellVTI(
                _VTIAmount: $VTIAmount,
                _VTIPrice: $VTIPrice,
                _userAddress: $userAddress,
                _timeStamp: $timeStamp
            );

            if($response['status']=='error'){
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message'=>'Что-то пошло не так, повторите операцию позже'],
                    []
                );
            }

        }elseif ($type == 'balance') {
            $response = Web3Service::sellVTIToBalance(
                _VTIAmount: $VTIAmount,
                _VTIPrice: $VTIPrice,
                _userAddress: $userAddress,
                _timeStamp: $timeStamp
            );

            if($response['status']=='error'){
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message'=>'Что-то пошло не так, повторите операцию позже'],
                    []
                );
            }

            //если успех то
            $user->wallet += $request->get('amount') * $indexLast->index;
            $user->save();

            ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $request->get('amount') * $indexLast->index,
                'type' => 'index_sell',
            ]);
        }

        $deposits = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', 0)
            ->all();
        $average_price = $indexLast->index;
        if($deposits) {
            $depositsCount = count($deposits);
            foreach ($deposits as $deposit) {
                $depositsSum += $deposit->price_index;
            }
            $average_price = round($depositsSum/$depositsCount, 2);
        }
        $difference = $indexLast->index - $average_price;
        $percentage = ($difference / $average_price) * 100;

        //аля для истории is_active у нас будет для отображения покупки/продажи
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount') * $indexLast->index,
            'count_index' => $request->get('amount'),
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => false,
            'program_id' => 0,
            'average_price' => $average_price,
            'result' => $percentage,
            'type' => 'now',
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function buyNow(Request $request)
    {
        $type = $request->get('type');

        $user = Auth::user();

        $sumLimit = TicketReport::where('user_id', $user->id)
            ->where('type', 'index_pay_' . $request->get('code'))
            ->sum('comment');

        $sumPay = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->sum('amount');

        $limit = $sumLimit - $sumPay;

        // проверяем лимит
        if($limit < $request->get('amount'))
        {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['message' => 'Превышен лимит'],
                []
            );
        }

        // проверяем баланс если это $type = 'balance'
        if($type == 'balance' && $user->wallet < $request->get('amount'))
        {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['message' => 'Недостаточно средств'],
                []
            );
        }

        // берем текущий курс
        Artisan::call('index:cron');
        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();
        $secondIndex = IndexToken::orderBy('id', 'desc')->skip(1)->take(1)->first();

        // Вычисляем порог для разницы в 5%
        $threshold = $secondIndex->index * 0.05;

        if ((abs($indexLast->index - $secondIndex->index) > $threshold) || $indexLast->index == 0) {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['message'=>'Неккоректный курс'],
                []
            );
        }

        // параметры для контракта
        $USDTAmount = $request->get('amount');
        $VTIPrice = $indexLast->index;
        $userAddress = $request->get('wallet_address');
        $timeStamp = Carbon::now()->timestamp;
        $openLimit = $limit;
        $code = $request->get('code');

        if($type == 'wallet') {
            //вызываешь контракт на покупку с кошелька
            $response = Web3Service::buyVTIfromWallet(
                _USDTAmount : $USDTAmount,
                _VTIPrice : $VTIPrice,
                _userAddress : $userAddress,
                _timeStamp : $timeStamp,
                _openLimit : $openLimit,
                _code : $code
            );

            if($response['status']=='error'){
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message'=>'Что-то пошло не так, повторите операцию позже'],
                    []
                );
            }
        }
        elseif ($type == 'balance') {
            //вызываешь контракт на покупку с баланса
            $response = Web3Service::buyVTIfromBalance(
                _USDTAmount : $USDTAmount,
                _VTIPrice : $VTIPrice,
                _userAddress : $userAddress,
                _code : $code
            );

            if($response['status']=='error'){
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message'=>'Что-то пошло не так, повторите операцию позже'],
                    []
                );
            }

            //минусуем с баланса если успех
            $user->wallet -= $request->get('amount');
            $user->save();

            ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $request->get('amount'),
                'type' => 'index_buy',
            ]);
        }

        $deposits = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->all();
        $average_price = $indexLast->index;
        if($deposits) {
            $depositsCount = count($deposits);
            foreach ($deposits as $deposit) {
                $depositsSum += $deposit->price_index;
            }
            $average_price = round($depositsSum/$depositsCount, 2);
        }

        // создаем депозит (история)
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount'),
            'count_index' => $request->get('amount') / $indexLast->index,
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => true,
            'program_id' => $request->get('code'),
            'average_price' => $average_price,
            'type' => 'now',
        ]);
    }

    public function buyVTIfromHandler(Request $request, $payNow = false){
        $user = $request->get('user')??null;
        if(!isset($user)){
            $user = Auth::user();
        }

        $sumLimit = TicketReport::where('user_id', $user->id)
            ->where('type', 'index_pay_' . $request->get('code'))
            ->sum('comment');

        $sumPay = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->sum('amount');

        $limit = $sumLimit - $sumPay;

        // проверяем лимит
        if($limit < $request->get('amount'))
        {
            return [
                'status'=>false,
                'message' => 'Превышен лимит',
                'code'=>1
            ];
        }

        // проверяем баланс если это $type = 'balance'
        if($user->wallet < $request->get('amount'))
        {
            return [
                'status'=>false,
                'message' => 'Недостаточно средств',
                'code'=>2
            ];
        }

        // берем текущий курс
        Artisan::call('index:cron');
        $indexLast = IndexToken::orderBy('id', 'desc')->first();
        $secondIndex = IndexToken::orderBy('id', 'desc')->skip(1)->take(1)->first();

        // Вычисляем порог для разницы в 5%
        $threshold = $secondIndex->index * 0.05;

        if ((abs($indexLast->index - $secondIndex->index) > $threshold) || $indexLast->index == 0) {
            return [
                'status'=>false,
                'message' => 'Неккоректный курс',
                'code'=>4
            ];
        }

        // параметры для контракта
        $USDTAmount = $request->get('amount');
        $VTIPrice = $indexLast->index;
        $userAddress = $request->get('wallet_address');
        $code = $request->get('code');

        //вызываешь контракт на покупку с баланса
        $response = Web3Service::buyVTIfromBalance(
            _USDTAmount : $USDTAmount,
            _VTIPrice : $VTIPrice,
            _userAddress : $userAddress,
            _code : $code
        );

        if($response['status']=='error'){
            return [
                'status'=>false,
                'message' => 'Что-то пошло не так, повторите операцию позже',
                'code'=>5,
                'error'=> json_encode($response)
            ];
        }

        //минусуем с баланса если успех
        $user->wallet -= $request->get('amount');
        $user->save();

        ReportReferral::create([
            'owner_id' => 1,
            'member_id' => $user->id,
            'sum' => $request->get('amount'),
            'type' => $payNow ? 'index_buy' : 'index_buy_auto_' . $code,
        ]);

        $deposits = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $code)
            ->all();
        $average_price = $indexLast->index;
        if($deposits) {
            $depositsCount = count($deposits);
            foreach ($deposits as $deposit) {
                $depositsSum += $deposit->price_index;
            }
            $average_price = round($depositsSum/$depositsCount, 2);
        }

        // создаем депозит (история)
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount'),
            'count_index' => $request->get('amount') / $indexLast->index,
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => true,
            'program_id' => $code,
            'average_price' => $average_price,
            'type' => 'now',
        ]);

        return [
            'status'=>true,
            'message' => 'success'
        ];
    }

    public function buyVTIfromHandlerAuto(Request $request, $payNow = false){
        $user = $request->get('user')??null;
        if(!isset($user)){
            $user = Auth::user();
        }

        $balance = IaSystem::where('user_id', $user->id)->first();

        $sumLimit = TicketReport::where('user_id', $user->id)
            ->where('type', 'index_pay_' . $request->get('code'))
            ->sum('comment');

        $sumPay = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('code'))
            ->sum('amount');

        $limit = $sumLimit - $sumPay;

        // проверяем лимит
        if($limit < $request->get('amount'))
        {
            return [
                'status'=>false,
                'message' => 'Превышен лимит',
                'code'=>1
            ];
        }

        // проверяем баланс если это $type = 'balance'
        if($balance->balance < $request->get('amount'))
        {
            return [
                'status'=>false,
                'message' => 'Недостаточно средств',
                'code'=>2
            ];
        }

        // берем текущий курс
        Artisan::call('index:cron');
        $indexLast = IndexToken::orderBy('id', 'desc')->first();
        $secondIndex = IndexToken::orderBy('id', 'desc')->skip(1)->take(1)->first();

        // Вычисляем порог для разницы в 5%
        $threshold = $secondIndex->index * 0.05;

        if ((abs($indexLast->index - $secondIndex->index) > $threshold) || $indexLast->index == 0) {
            return [
                'status'=>false,
                'message' => 'Неккоректный курс',
                'code'=>4
            ];
        }

        // параметры для контракта
        $USDTAmount = $request->get('amount');
        $VTIPrice = $indexLast->index;
        $userAddress = $request->get('wallet_address');
        $code = $request->get('code');

        //вызываешь контракт на покупку с баланса
        $response = Web3Service::buyVTIfromBalance(
            _USDTAmount : $USDTAmount,
            _VTIPrice : $VTIPrice,
            _userAddress : $userAddress,
            _code : $code
        );

        if($response['status']=='error'){
            return [
                'status'=>false,
                'message' => 'Что-то пошло не так, повторите операцию позже',
                'code'=>5,
                'error'=> json_encode($response)
            ];
        }

        //минусуем с баланса если успех
        $balance->balance -= $request->get('amount');
        $balance->save();

        IaSystemReport::create([
            'user_id' => $user->id,
            'sum' => $request->get('amount'),
            'count_pay' => $request->get('amount') / $indexLast->index,
            'type' => 'index_buy_auto_' . $code,
        ]);

        $deposits = IndexDeposit::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('program_id', $code)
            ->all();
        $average_price = $indexLast->index;
        if($deposits) {
            $depositsCount = count($deposits);
            foreach ($deposits as $deposit) {
                $depositsSum += $deposit->price_index;
            }
            $average_price = round($depositsSum/$depositsCount, 2);
        }

        // создаем депозит (история)
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount'),
            'count_index' => $request->get('amount') / $indexLast->index,
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => true,
            'program_id' => $code,
            'average_price' => $average_price,
            'type' => 'now',
        ]);

        return [
            'status'=>true,
            'message' => 'success'
        ];
    }
}
