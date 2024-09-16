<?php

namespace App\Http\Controllers\V1;

use App\Models\ArbBalance;
use App\Models\ArbDeposit;
use App\Models\ReportReferral;
use App\Models\TicketReport;
use App\Models\UsdtTransaction;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function pay(Request $request)
    {
        if($request->get('product') == 'arb_deposit') {

            $request->validate([
                'amount' => 'required|numeric|min:1',
                'count_months' => 'required|numeric|in:3,6,9,12,18',
            ]);

            $user = Auth::user();

            if($request->get('count_months') == 3) {
                $percent = 5;
            }
            elseif($request->get('count_months') == 6) {
                $percent = 6;
            }
            elseif($request->get('count_months') == 9) {
                $percent = 6.5;
            }
            elseif($request->get('count_months') == 12) {
                $percent = 7;
            }
            elseif($request->get('count_months') == 18) {
                $percent = 8;
            }

            $arbBalance = ArbBalance::where('user_id', $user->id)->first();
            $arbSum = ArbDeposit::where('user_id', $user->id)->sum('amount');
            if($arbBalance->can_pay < ($request->get('amount') + $arbSum)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно оплачено тикетов'
                ]);
            }

            if($user->wallet < $request->get('amount')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Недостаточно средств'
                ]);
            }

            $user->wallet -= $request->get('amount');
            $user->save();

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $request->get('amount'),
                'type' => 'arb_deposit',
            ]);
            $rep->type = 'arb_deposit';
            $rep->save();

            ArbDeposit::create([
                'user_id' => $user->id,
                'amount' => $request->get('amount'),
                'count_months' => $request->get('count_months'),
                'percent' => $percent,
            ]);

            UsdtTransaction::create([
                'user_id' => $user->id,
                'transaction_id' => 'balance',
                'sum_usd' => $request->get('amount'),
                'address' => 'balance',
                'product' => $request->get('product'),
                'date' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Депозит успешно создан'
            ]);
        }

        if($request->get('product') == 'account' ||
            $request->get('product') == 'bronze' ||
            $request->get('product') == 'silver' ||
            $request->get('product') == 'gold' ||
            $request->get('product') == 'platinum')
        {
            $user = Auth::user();

            if($request->get('amount') > $user->wallet) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    [],
                    [
                        'Недостаточно средств на балансе'
                    ]
                );
            }

            UsdtTransaction::transactionPayed($user->id, $request->get('product'), $request->get('amount'), true);

            UsdtTransaction::create([
                'user_id' => $user->id,
                'transaction_id' => 'balance',
                'sum_usd' => $request->get('amount'),
                'address' => 'balance',
                'product' => $request->get('product'),
                'date' => Carbon::now()
            ]);

            $rep = ReportReferral::where('owner_id', 1)
                ->where('member_id', $user->id)
                ->where('type', $request->get('product'))
                ->orderBy('id', 'desc')
                ->first();

            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                [
                    'Успешно'
                ]
            );
        }

        if ($request->get('product') == 'life_1' ||
            $request->get('product') == 'life_2' ||
            $request->get('product') == 'life_3' ||
            $request->get('product') == 'life_4' ||
            $request->get('product') == 'life_5' ||
            $request->get('product') == 'life_6') {
            if ($request->get('product') == 'life_1') {
                $sum = 30;
            } elseif ($request->get('product') == 'life_2') {
                $sum = 50;
            } elseif ($request->get('product') == 'life_3') {
                $sum = 100;
            } elseif ($request->get('product') == 'life_4') {
                $sum = 300;
            } elseif ($request->get('product') == 'life_5') {
                $sum = 500;
            } elseif ($request->get('product') == 'life_6') {
                $sum = 1000;
            }

            $user = Auth::user();

            if($sum > $user->wallet) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    [],
                    [
                        'Недостаточно средств на балансе'
                    ]
                );
            }

            $trans = UsdtTransaction::where('user_id', $user->id)
                ->where('product', $request->get('product'))
                ->first();
            if($trans) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    [],
                    [
                        'У вас уже есть этот продукт'
                    ]
                );
            }

            $user->wallet -= $sum;
            $user->save();

            UsdtTransaction::transactionPayed($user->id, $request->get('product'), $sum, true);

            UsdtTransaction::create([
                'user_id' => $user->id,
                'transaction_id' => 'balance',
                'sum_usd' => $sum,
                'address' => 'balance',
                'product' => $request->get('product'),
                'date' => Carbon::now()
            ]);

            ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $sum,
                'type' => $request->get('product') . '_pay',
            ]);

            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                [
                    'Успешно'
                ]
            );
        }

        if($request->get('product') == 'token_private')
        {
            $user = Auth::user();
            $countNeedTickets = intdiv($request->get('amount'), 995);

            $private_can_pay = TicketReport::where('user_id', $user->id)->where('type', 'private')->where('created_at', '>=', '2023-11-14 00:00:00')->sum('count') / 10 ?? 0;
            $private_pay = UsdtTransaction::where('user_id', $user->id)->where('product', 'token_private')->where('created_at', '>=', '2023-11-14 00:00:00')->sum('sum_usd') / 1000 ?? 0;
            if($private_pay != 0) {
                $can_pay = $private_can_pay - $private_pay;
            }
            else {
                $can_pay = $private_can_pay;
            }

            if($request->get('amount') > $user->wallet) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Недостаточно средств на балансе'],
                    []
                );
            }

            if($can_pay < $countNeedTickets) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    ['Не хватает лимита'],
                    []
                );
            }

            $user->wallet -= $request->get('amount');
            $user->save();

            UsdtTransaction::transactionPayed($user->id, $request->get('product'), $request->get('amount'), true);

            UsdtTransaction::create([
                'user_id' => $user->id,
                'transaction_id' => 'balance',
                'sum_usd' => $request->get('amount'),
                'address' => 'balance',
                'product' => $request->get('product'),
                'date' => Carbon::now()
            ]);

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $request->get('amount'),
                'type' => $request->get('product'),
            ]);

            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                [
                    'Успешно'
                ]
            );
        }
    }
}
