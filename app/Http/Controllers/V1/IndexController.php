<?php

namespace App\Http\Controllers\V1;

use App\Models\IndexAutoPurchase;
use App\Models\IndexBalance;
use App\Models\IndexDeposit;
use App\Models\IndexToken;
use App\Models\IndexTokenInfo;
use App\Models\ReportReferral;
use App\Models\TicketReport;
use App\Models\User;
use App\Services\Response\ResponseService;
use App\Services\Web3Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    public function buyVTI(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'wallet_address' => 'required|string',
            'type' => 'required|string',
            'code' => 'required|integer',
        ]);

        $return = $this->buyNow($request);

//        if(isset($return)){
//            return $return;
//        }else{
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                $return
            );
//        }
    }

    public function sellVTI(Request $request) {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'wallet_address' => 'required|string',
        ]);

        $user = Auth::user();

        // берем текущий курс
        Artisan::call('index:cron');
        $indexLast = IndexToken::orderBy('id', 'desc')->first();
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


        //аля для истории is_active у нас будет для отображения покупки/продажи
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount') * $indexLast->index,
            'count_index' => $request->get('amount'),
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => false,
            'program_id' => 0,
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function wallets(Request $request): JsonResponse
    {
        $request->validate([
            'wallet_address' => 'required|string',
        ]);

        $user = Auth::user();

        foreach($user->cryptoWallets as $wallet) {
            $wallet->is_active = false;
            $wallet->save();
        }

        $user->cryptoWallets()->updateOrCreate(
            ['wallet_address' => $request->get('wallet_address')],
            ['is_active' => true]
        );

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function wantToBuy(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $user = Auth::user();
        $indexBalance = IndexBalance::where('user_id', $user->id)->first();

        if($indexBalance->can_pay < $request->get('amount'))
        {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['message' => 'Превышен лимит'],
                []
                []
            );
        }

        Artisan::call('index:cron');
        $indexLast = IndexToken::orderBy('id', 'desc')->first();

//        $walletActive = $user->wallets()->where('is_active', true)->first();

        $response = [
            'index_token_price' => $indexLast->index,
            'date_time' => Carbon::now(),
//            'wallet_address' => $walletActive->wallet_address,
        ];

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function index(Request $request)
    {
        $indexLast = IndexToken::orderBy('id', 'desc')->first();

        $indexTokenInfos = IndexTokenInfo::select('title', 'key', 'icon')->get();
        foreach($indexTokenInfos as $indexTokenInfo)
        {
            if($indexTokenInfo->key == 'bitcoin')
            {$indexTokenInfo->percent = intval($indexLast->bitcoin * 100);}
            if($indexTokenInfo->key == 'ethereum')
            {$indexTokenInfo->percent = intval($indexLast->ethereum * 100);}
            if($indexTokenInfo->key == 'arbitrum')
            {$indexTokenInfo->percent = intval($indexLast->arbitrum * 100);}
            if($indexTokenInfo->key == 'optimism')
            {$indexTokenInfo->percent = intval($indexLast->optimism * 100);}
            if($indexTokenInfo->key == 'polygon')
            {$indexTokenInfo->percent = intval($indexLast->polygon * 100);}
            if($indexTokenInfo->key == 'polkadot')
            {$indexTokenInfo->percent = intval($indexLast->polkadot * 100);}
            if($indexTokenInfo->key == 'ton')
            {$indexTokenInfo->percent = intval($indexLast->ton * 100);}
            if($indexTokenInfo->key == 'solana')
            {$indexTokenInfo->percent = intval($indexLast->solana * 100);}
            if($indexTokenInfo->key == 'apecoin')
            {$indexTokenInfo->percent = intval($indexLast->apecoin * 100);}
        }

        $indexBalance = IndexBalance::where('user_id', Auth::id())->first();
        if (!isset($indexBalance)) {
            $indexBalance = IndexBalance::create([
                'user_id' => Auth::id(),
                'can_pay' => 0,
            ]);
        }

        $response = [
            'index_token_price' => $indexLast->index,
            'wallet' => [
                'all_sum' => IndexDeposit::where('user_id', Auth::id())->where('is_active', true)->sum('amount'),
                'count_index' => IndexDeposit::where('user_id', Auth::id())->where('is_active', true)->sum('count_index'),
                'all_sum_pay' => IndexDeposit::where('user_id', Auth::id())->where('is_active', true)->sum('amount'),
//                'can_pay' => $indexBalance->can_pay,
                'result_usdt' => 0,
                'result_percent' => 0,
            ],
            'index_statistic' => [],
            'index_compound' => $indexTokenInfos,
            'index_history' => IndexDeposit::where('user_id', Auth::id())->orderBy('id', 'desc')->get(),
        ];

        $timeFilter = $request->get('time_filter', 'all'); // Здесь используем параметр 'time_filter' из запроса

        // В зависимости от выбранного фильтра времени, получаем статистику
        switch ($timeFilter) {
            case 'week':
                $startOfWeek = now()->subWeek();
                $endOfWeek = now();
                $statistics = IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->orderBy('id', 'asc')
                    ->get();
                break;
            case 'month':
                $startOfMonth = now()->subMonth();
                $endOfMonth = now();
                $statistics = IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('id', 'asc')
                    ->get();
                break;
            case 'all':
            default:
                $statistics = IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->orderBy('id', 'asc')
                    ->get();
                break;
        }

        foreach ($statistics as $statistic)
        {
            $response['index_statistic'][] = [
                'date' => $statistic->created_at->format('Y-m-d H:i:s'),
                'index' => $statistic->index,
            ];
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function lastWallet():string
    {
        $user = Auth::user();

        // Получаем последний кошелек пользователя
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

        return $lastWallet;
    }

    public function programs(): JsonResponse
    {
        $user = Auth::user();

        // Получаем последний кошелек пользователя
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
        // место для твоего кода <3
        $programs = Web3Service::getAllUserPrograms(wallet:$lastWallet);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $programs
        );
    }

    public function checkLimitProgram(Request $request): JsonResponse
    {
        $request->validate([
            'program_id' => 'required|integer',
        ]);

        $sumLimit = TicketReport::where('user_id', Auth::user()->id)
            ->where('type', 'index_pay_' . $request->get('program_id'))
            ->sum('comment');

        $sumPay = IndexDeposit::where('user_id', Auth::user()->id)
            ->where('is_active', true)
            ->where('program_id', $request->get('program_id'))
            ->sum('amount');

        $responce = [
            'limit' => $sumLimit - $sumPay
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $responce
        );
    }
    public function getAllAutoPurchases(){
        $user = Auth::user();
        $purchases = IndexAutoPurchase::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [$purchases??[]]
        );
    }
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
    public function getAutoPurchase(int $id, Request $request){
        $purchase = IndexAutoPurchase::where('user_id', Auth::user()->id)->where('program_id', $id)->orderBy('created_at', 'desc')->first();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [$purchase]
        );
    }

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
        $indexLast = IndexToken::orderBy('id', 'desc')->first();
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

//            if($response['status']=='error'){
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    ['message'=>'Что-то пошло не так, повторите операцию позже'],
                    [$response]
                );
//            }
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

        // создаем депозит (история)
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount'),
            'count_index' => $request->get('amount') / $indexLast->index,
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => true,
            'program_id' => $request->get('code'),
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

        // создаем депозит (история)
        IndexDeposit::create([
            'user_id' => $user->id,
            'amount' => $request->get('amount'),
            'count_index' => $request->get('amount') / $indexLast->index,
            'price_index' => $indexLast->index,
            'start' => Carbon::now(),
            'is_active' => true,
            'program_id' => $code,
        ]);

        return [
            'status'=>true,
            'message' => 'success'
        ];
    }

    public function adminAutoPay(Request $request)
    {
        $request->validate([
            'regularity' => 'required|string|in:week,month',
        ]);

        $autoPays = IndexAutoPurchase::where('regularity', $request->get('regularity'))
            ->orderBy('id', 'desc')
            ->paginate(10);

        foreach ($autoPays as $autoPay) {
            $autoPay->user = User::find($autoPay->user_id);
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'autoPays' => $autoPays,
            ]
        );
    }
}
