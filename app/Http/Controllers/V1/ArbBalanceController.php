<?php

namespace App\Http\Controllers\V1;

use App\Models\ArbBalance;
use App\Models\ArbDeposit;
use App\Models\PoolPercent;
use App\Models\ReportReferral;
use App\Models\Seling;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArbBalanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $arbBalance = ArbBalance::where('user_id', $user->id)->first();
        if (!$arbBalance) {
            $arbBalance = ArbBalance::create([
                'user_id' => $user->id,
                'can_pay' => 0,
            ]);
        }

        $response = [
            'balance' => ArbDeposit::where('user_id', $user->id)->where('is_active', true)->where('is_request', false)->sum('amount'),
            'active' => ArbDeposit::where('user_id', $user->id)->where('is_active', true)->where('is_request', false)->count(),
            'can_money_request' => [
                'sum' => ArbDeposit::where('user_id', $user->id)->where('is_active', true)->where('is_can_request', true)->sum('amount'),
                'deposits' => ArbDeposit::where('user_id', $user->id)->where('is_active', true)->where('is_can_request', true)->get(),
            ],
            'can_pay' => [
                'sum_deposits' => ArbDeposit::where('user_id', $user->id)->sum('amount'),
                'can_pay_sum' => $arbBalance->can_pay,
            ],
            'history' => ArbDeposit::where('user_id', $user->id)->orderBy('id', 'desc')->get(),
        ];

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function reopen(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $deposit = ArbDeposit::where([
            'id' => $request->get('id'),
            'user_id' => Auth::user()->id,
        ])
            ->first();

        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Депозит не найден.'
            ]);
        }
        if ($deposit->is_active === true) {
            return response()->json([
                'success' => false,
                'message' => 'Депозит активен, закрыть нельзя.'
            ]);
        }
        if ($deposit->is_wont_request === true) {
            return response()->json([
                'success' => false,
                'message' => 'Депозит поставлен на вывод, закрыть нельзя.'
            ]);
        }

        $deposit->is_active = true;
        $deposit->is_can_request = false;
        $deposit->start = Carbon::now();
        $deposit->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $deposit
        );
    }

    public function change(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'count_months' => 'required|integer|in:6,12,18'
        ]);

        $deposit = ArbDeposit::where([
            'id' => $request->get('id'),
            'user_id' => Auth::user()->id,
        ])
            ->first();

        if (!$deposit) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['Депозит не найден.'],
                $deposit
            );
        }

        $percent = $deposit->percent;
        if ($request->get('count_months') == 6) {
            $percent = 6;
        } elseif ($request->get('count_months') == 12) {
            $percent = 7;
        } elseif ($request->get('count_months') == 18) {
            $percent = 8;
        }

        $deposit->percent = $percent;
        $deposit->is_active = true;
        $deposit->is_can_request = false;
        $deposit->is_wont_request = false;
        $deposit->start = Carbon::now();
        $deposit->count_months = $request->get('count_months');
        $deposit->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $deposit
        );
    }

    public function startArb(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $arbDeposit = ArbDeposit::findOrFail($request->get('id'));

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

    public function allDeposits()
    {
        $response = [];

        $deps = ArbDeposit::orderBy('id', 'desc')->paginate(10);
        foreach ($deps as $dep) {
            $dep['user'] = User::find($dep->user_id);
        }
        $response['deposits'] = $deps;

        $response['short-info'] = [
            'all_sum' => ArbDeposit::where('start', '!=', null)
                ->where('is_active', true)
                ->sum('amount'),
            'count' => ArbDeposit::where('start', '!=', null)
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
        $deps = ArbDeposit::where('is_wont_request', true)->orderBy('updated_at', 'desc')->paginate(10);
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

    public function requestMoney(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $arbDeposit = ArbDeposit::findOrFail($request->get('id'));

        if (!$arbDeposit->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Депозит не активен'
            ]);
        }

        $arbDeposit->is_wont_request = true;
        $arbDeposit->save();

        return response()->json([
            'success' => true,
            'message' => 'Депозит закрыт',
            'data' => $arbDeposit
        ]);
    }

    public function close(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $arbDeposit = ArbDeposit::findOrFail($request->get('id'));

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
            'type' => 'arb_request',
            'data' => [
                'balance' => $user->wallet,

            ]
        ]);
        $rep->type = 'arb_request';
        $rep->save();

        return response()->json([
            'success' => true,
            'message' => 'Депозит закрыт',
            'data' => $arbDeposit
        ]);
    }

    public function arbPools()
    {
        $user = Auth::user();

        $personal_investment = Seling::where('member_id', $user->id)->where('product_id', 'arb_deposit')->where('line', 0)->get();
        $investment_first_line = Seling::where('member_id', $user->id)->where('product_id', 'arb_deposit')->where('line', 1)->get();
        $investment_other_line = Seling::where('member_id', $user->id)->where('product_id', 'arb_deposit')
            ->where(function ($query) {
                $query->where('line', 2)
                    ->orWhere('line', 3)
                    ->orWhere('line', 4)
                    ->orWhere('line', 5);
            })->get();

        $sum1 = $user->commission * $personal_investment->sum('sum');
        $sum1_bronze = $personal_investment->sum('sum') * 0.5;
        $sum1_silver = $personal_investment->sum('sum') * 0.7;
        $sum1_gold = $personal_investment->sum('sum') * 1;
        $sum1_platinum = $personal_investment->sum('sum') * 1;

        $sum2 = $user->commission * $investment_first_line->sum('sum');
        $sum2_bronze = $investment_first_line->sum('sum') * 0.5;
        $sum2_silver = $investment_first_line->sum('sum') * 0.7;
        $sum2_gold = $investment_first_line->sum('sum') * 1;
        $sum2_platinum = $investment_first_line->sum('sum') * 1;

        $sum3 = $user->commission / 10 * $investment_other_line->sum('sum');
        $sum3_bronze = $investment_other_line->sum('sum') * 0.05;
        $sum3_silver = $investment_other_line->sum('sum') * 0.07;
        $sum3_gold = $investment_other_line->sum('sum') * 0.1;
        $sum3_platinum = $investment_other_line->sum('sum') * 0.1;

        $users1 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 5000')
            ->get();
        $ids1 = [1, 8475];
        foreach ($users1 as $user) {
            $ids1[] = $user->id;
        }
        $ids1 = array_unique($ids1);
        $percent1 = PoolPercent::where('key', 'pool-arb-1')->first()->percent;

        $users2 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 15000')
            ->get();
        $ids2 = [1, 8475];
        foreach ($users2 as $user) {
            $ids2[] = $user->id;
        }
        $ids2 = array_unique($ids2);
        $percent2 = PoolPercent::where('key', 'pool-arb-2')->first()->percent;

        $users3 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 25000')
            ->get();
        $ids3 = [1, 8475];
        foreach ($users3 as $user) {
            $ids3[] = $user->id;
        }
        $ids3 = array_unique($ids3);
        $percent3 = PoolPercent::where('key', 'pool-arb-3')->first()->percent;

        $users4 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 50000')
            ->get();
        $ids4 = [1, 8475];
        foreach ($users4 as $user) {
            $ids4[] = $user->id;
        }
        $ids4 = array_unique($ids4);
        $percent4 = PoolPercent::where('key', 'pool-arb-4')->first()->percent;

        $users5 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'arb_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 100000')
            ->get();
        $ids5 = [1, 8475];
        foreach ($users5 as $user) {
            $ids5[] = $user->id;
        }
        $ids5 = array_unique($ids5);
        $percent5 = PoolPercent::where('key', 'pool-arb-5')->first()->percent;

        $plats = UsdtTransaction::where(function ($query) {
            $query->where('product', 'platinum')
                ->orWhere('product', 'platinum_pay');
        })
            ->get();

        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }
        $platinumUsers = User::whereIn('id', $platinumUsers)->get();

        $transactions = ArbDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount') * 0.5;

        $user = Auth::user();

        $response = [
            'status' => UsdtTransaction::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('product', 'bronze')
                        ->orWhere('product', 'silver')
                        ->orWhere('product', 'gold')
                        ->orWhere('product', 'platinum')
                        ->orWhere('product', 'bronze_pay')
                        ->orWhere('product', 'silver_pay')
                        ->orWhere('product', 'gold_pay')
                        ->orWhere('product', 'platinum_pay');
                })
                ->orderBy('id', 'desc')
                ->first()->product ?? 'basic',
            'personal_investment' => [
                'sum_all' => $personal_investment->sum('sum'),
                'offset' => [
                    'sum' => $sum1,
                    'percent' => $user->commission * 100,
                ]
            ],
            'investment_first_line' => [
                'sum_all' => $investment_first_line->sum('sum'),
                'offset' => [
                    'sum' => $sum2,
                    'percent' => $user->commission * 100,
                ]
            ],
            'investment_other_line' => [
                'sum_all' => $investment_other_line->sum('sum'),
                'offset' => [
                    'sum' => $sum3,
                    'percent' => $user->commission * 10,
                ]
            ],
            'all_offset' => $sum1 + $sum2 + $sum3,
            'other_statuses' => [
                'bronze' => $sum1_bronze + $sum2_bronze + $sum3_bronze,
                'silver' => $sum1_silver + $sum2_silver + $sum3_silver,
                'gold' => $sum1_gold + $sum2_gold + $sum3_gold,
                'platinum' => $sum1_platinum + $sum2_platinum + $sum3_platinum,
            ],
            'pool1' => [
                'count_users' => count($ids1),
                'sum' => round($transactions * 0.5, 2),
                'sum_week' => round($transactions * $percent1 / count($ids1) * 0.5, 2),
                'need_sum' => '5000',
                'percent' => $percent1 * 4 * 100
            ],
            'pool2' => [
                'count_users' => count($ids2),
                'sum' => round($transactions * 0.5, 2),
                'sum_week' => round($transactions * $percent2 / count($ids2) * 0.5, 2),
                'need_sum' => '15000',
                'percent' => $percent2 * 4 * 100
            ],
            'pool3' => [
                'count_users' => count($ids3),
                'sum' => round($transactions * 0.5, 2),
                'sum_week' => round($transactions * $percent3 / count($ids3) * 0.5, 2),
                'need_sum' => '25000',
                'percent' => $percent3 * 4 * 100
            ],
            'pool4' => [
                'count_users' => count($ids4),
                'sum' => round($transactions * 0.5, 2),
                'sum_week' => round($transactions * $percent4 / count($ids4) * 0.5, 2),
                'need_sum' => '50000',
                'percent' => $percent4 * 4 * 100
            ],
            'pool5' => [
                'count_users' => count($ids5),
                'sum' => round($transactions * 0.5, 2),
                'sum_week' => round($transactions * $percent5 / count($ids5) * 0.5, 2),
                'need_sum' => '100000',
                'percent' => $percent5 * 4 * 100
            ],
            'pool6' => [
                'count_users' => count($platinumUsers) - 4,
                'sum' => $transactions / 2,
                'sum_week' => round($transactions / 100 / 2 / (count($platinumUsers) - 4) / 4, 2),
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Ннформация о пулах',
            'data' => $response
        ]);
    }
}
