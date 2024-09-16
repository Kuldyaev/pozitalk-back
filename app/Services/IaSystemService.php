<?php

namespace App\Services;

use App\Models\ArbBalance;
use App\Models\ArbDeposit;
use App\Models\IaSystem;
use App\Models\IaSystemDeposit;
use App\Models\IaSystemReport;
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

class IaSystemService
{
    const POOL_6_CONDITIONS = ['platinum', 'platinum_pay'];

    public function iaSystemPools(): array
    {
        $user = Auth::user();

        $userSumArb = IaSystemDeposit::where('user_id', $user->id)->where('is_active', true)->sum('amount');

        $userStatus = UsdtTransaction::where('user_id', $user->id)
            ->where( function ($query) {
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
            ->first()->product ?? 'basic';

        $users1 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 5000')
            ->get();
        $ids1 = [1,8475];
        foreach ($users1 as $user) {
            $ids1[] = $user->id;
        }
        $ids1 = array_unique($ids1);
        $percent = PoolPercent::where('key', 'ia-system-pool')->first()->percent / 100;

        $users2 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 15000')
            ->get();
        $ids2 = [1,8475];
        foreach ($users2 as $user) {
            $ids2[] = $user->id;
        }
        $ids2 = array_unique($ids2);

        $users3 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 25000')
            ->get();
        $ids3 = [1,8475];
        foreach ($users3 as $user) {
            $ids3[] = $user->id;
        }
        $ids3 = array_unique($ids3);

        $users4 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 50000')
            ->get();
        $ids4 = [1,8475];
        foreach ($users4 as $user) {
            $ids4[] = $user->id;
        }
        $ids4 = array_unique($ids4);

        $users5 = DB::table('users')
            ->join('selings', 'users.id', '=', 'selings.member_id')
            ->select('users.id', 'users.commission')
            ->where('selings.product_id', '=', 'ia_system_deposit')
            ->groupBy('users.id', 'users.commission')
            ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 100000')
            ->get();
        $ids5 = [1,8475];
        foreach ($users5 as $user) {
            $ids5[] = $user->id;
        }
        $ids5 = array_unique($ids5);

        $plats = UsdtTransaction::where(function($query) {
            $query->where('product', 'platinum')
                ->orWhere('product', 'platinum_pay');
            })
            ->get();

        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }
        $platinumUsers = User::whereIn('id', $platinumUsers)->get();

        $transactions = IaSystemDeposit::where('start', '!=', null)
            ->where('is_active', true)
            ->sum('amount');

        $personal_investment = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')->where('line', 0)->sum('sum') * $user->commission;
        $investment_first_line = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')->where('line', 1)->sum('sum') * $user->commission;
        $investment_other_line = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')
            ->where(function ($query) {
                $query->where('line', 2)
                    ->orWhere('line', 3)
                    ->orWhere('line', 4)
                    ->orWhere('line', 5);
            })->sum('sum') * ($user->commission / 10);
        $sum = $personal_investment + $investment_first_line + $investment_other_line;

        $response = [
            'pool1' => [
                'count_users' => count($ids1),
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions * $percent, 2),
                'sum_week' => round($transactions * $percent / count($ids1), 2),
                'sum_month' => round($transactions * $percent / count($ids1), 2) * 4,
                'conditions' => 5000,
                'percent' => $percent * 100,
                'is_active' => $sum >= 5000,
            ],
            'pool2' => [
                'count_users' => count($ids2),
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions * $percent, 2),
                'sum_week' => round($transactions * $percent / count($ids2), 2),
                'sum_month' => round($transactions * $percent / count($ids2), 2) * 4,
                'conditions' => 15000,
                'percent' => $percent * 100,
                'is_active' => $sum >= 15000,
            ],
            'pool3' => [
                'count_users' => count($ids3),
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions * $percent, 2),
                'sum_week' => round($transactions * $percent / count($ids3), 2),
                'sum_month' => round($transactions * $percent / count($ids3), 2) * 4,
                'conditions' => 25000,
                'percent' => $percent * 100,
                'is_active' => $sum >= 25000,
            ],
            'pool4' => [
                'count_users' => count($ids4),
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions * $percent, 2),
                'sum_week' => round($transactions * $percent / count($ids4), 2),
                'sum_month' => round($transactions * $percent / count($ids4), 2) * 4,
                'conditions' => 50000,
                'percent' => $percent * 100,
                'is_active' => $sum >= 50000,
            ],
            'pool5' => [
                'count_users' => count($ids5),
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions * $percent, 2),
                'sum_week' => round($transactions * $percent / count($ids5), 2),
                'sum_month' => round($transactions * $percent / count($ids5), 2) * 4,
                'conditions' => 100000,
                'percent' => $percent * 100,
                'is_active' => $sum > 100000,
            ],
            'pool6' => [
                'count_users' => count($platinumUsers) - 4,
                'current_conditions' => $sum,
                'sum' => $transactions,
                'sum_distributed' => round($transactions / 100, 2),
                'sum_week' => round($transactions / 100 / 2 / (count($platinumUsers) - 4) / 4, 2),
                'sum_month' => round($transactions / 100 / 2 / (count($platinumUsers) - 4) / 4, 2) * 4,
                'conditions' => '',
                'is_active' => in_array($userStatus, self::POOL_6_CONDITIONS),
            ],
        ];

        return $response;
    }

    public function history($request)
    {
        $field = 'id';
        $order = 'desc';

        $arbDeposits = IaSystemDeposit::where('user_id', auth()->user()->id)
            ->orderBy(
                $request->get('field') ?? $field,
                $request->get('order') ?? $order
            )
            ->get();

        return $arbDeposits;
    }

    public function reopen($request)
    {
        $deposit = IaSystemDeposit::where([
                'id' => $request->get('id'),
                'user_id' => Auth::user()->id,
            ])
            ->first();

        $deposit->is_active = true;
        $deposit->is_can_request = false;
        $deposit->start = Carbon::now();
        $deposit->save();

        return $deposit;
    }

    public function change($request)
    {
        $deposit = IaSystemDeposit::where([
                'id' => $request->get('id'),
                'user_id' => Auth::user()->id,
            ])
            ->first();

        $deposit->is_active = true;
        $deposit->is_can_request = false;
        $deposit->is_wont_request = false;
        $deposit->start = Carbon::now();
        $deposit->count_months = $request->get('count_months');
        $deposit->save();

        return $deposit;
    }

    public function statistic(): array
    {
        $user = auth()->user();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);

        $sum_week = IaSystemReport::where('user_id', $user->id)->where('type', 'like', 'index_buy_auto_%')->sum('sum') * 0.03 / 4 ;
        $accumulated = IaSystemReport::where('user_id', $user->id)->sum('count_pay');
        $pool_sum = ReportReferral::where('member_id', $user->id)->where('type', 'like', 'pool-ia-system-%')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->sum('sum');

        $result = [
            'accumulated_vbti' => $accumulated,
            'sum_week' => $sum_week,
            'sum_pools' => $pool_sum,
        ];

        return $result;
    }

    public function calculationPools(): array
    {
        $user = Auth::user();

        $personal_investment = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')->where('line', 0)->get();
        $investment_first_line = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')->where('line', 1)->get();
        $investment_other_line = Seling::where('member_id', $user->id)->where('product_id', 'ia_system_deposit')
            ->where(function ($query) {
                $query->where('line', 2)
                    ->orWhere('line', 3)
                    ->orWhere('line', 4)
                    ->orWhere('line', 5);
            })->get();

        $result = [
            'personal_investment' => [
                'sum' => $personal_investment->sum('sum') * $user->commission,
                'percent' => $user->commission * 100,
                'sum_all' => $personal_investment->sum('sum'),
            ],
            'investment_first_line' => [
                'sum' => $investment_first_line->sum('sum') * $user->commission,
                'percent' => $user->commission * 100,
                'sum_all' => $investment_first_line->sum('sum'),
            ],
            'investment_other_line' => [
                'sum' => $investment_other_line->sum('sum') * ($user->commission / 10),
                'percent' => $user->commission * 10,
                'sum_all' => $investment_other_line->sum('sum'),
            ],
        ];

        return $result;
    }

    public function sumPools($user): float|int
    {
        $sum_pools = 0;

        $sumPools = Seling::where('member_id', $user->id)->where('product_id', 'arb_deposit')
            ->where(function ($query) {
                $query->where('line', 0)
                    ->orWhere('line', 1)
                    ->orWhere('line', 2)
                    ->orWhere('line', 3)
                    ->orWhere('line', 4)
                    ->orWhere('line', 5);
            })->sum('sum');

        if ($sumPools >= 5000) {
            $users = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->select('users.id', 'users.commission')
                ->where('selings.product_id', '=', 'ia_system_deposit')
                ->groupBy('users.id', 'users.commission')
                ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 5000')
                ->get();
            $ids = [1,8475];
            foreach ($users as $user) {
                $ids[] = $user->id;
            }
            $users = User::whereIn('id', array_unique($ids))->get();

            $transactions = ArbDeposit::where('start', '!=', null)
                    ->where('is_active', true)
                    ->sum('amount') * 0.5 * PoolPercent::where('key', 'pool-arb-1')->first()->percent;

            $sum_pools += $transactions / count($users) ?? 0;
        }
        if ($sumPools >= 15000) {
            $users = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->select('users.id', 'users.commission')
                ->where('selings.product_id', '=', 'ia_system_deposit')
                ->groupBy('users.id', 'users.commission')
                ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 15000')
                ->get();
            $ids = [1,8475];
            foreach ($users as $user) {
                $ids[] = $user->id;
            }
            $users = User::whereIn('id', array_unique($ids))->get();

            $transactions = ArbDeposit::where('start', '!=', null)
                    ->where('is_active', true)
                    ->sum('amount') * 0.5 * PoolPercent::where('key', 'pool-arb-2')->first()->percent;

            $sum_pools = $transactions / count($users) ?? 0;
        }
        if ($sumPools >= 25000) {
            $users = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->select('users.id', 'users.commission')
                ->where('selings.product_id', '=', 'ia_system_deposit')
                ->groupBy('users.id', 'users.commission')
                ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 25000')
                ->get();
            $ids = [1,8475];
            foreach ($users as $user) {
                $ids[] = $user->id;
            }
            $users = User::whereIn('id', array_unique($ids))->get();

            $transactions = ArbDeposit::where('start', '!=', null)
                    ->where('is_active', true)
                    ->sum('amount') * 0.5 * PoolPercent::where('key', 'pool-arb-3')->first()->percent;

            $sum_pools = $transactions / count($users) ?? 0;
        }
        if ($sumPools >= 50000) {
            $users = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->select('users.id', 'users.commission')
                ->where('selings.product_id', '=', 'ia_system_deposit')
                ->groupBy('users.id', 'users.commission')
                ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 50000')
                ->get();
            $ids = [1,8475];
            foreach ($users as $user) {
                $ids[] = $user->id;
            }
            $users = User::whereIn('id', array_unique($ids))->get();

            $transactions = ArbDeposit::where('start', '!=', null)
                    ->where('is_active', true)
                    ->sum('amount') * 0.5 * PoolPercent::where('key', 'pool-arb-4')->first()->percent;

            $sum_pools = $transactions / count($users) ?? 0;
        }
        if ($sumPools >= 100000) {
            $users = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->select('users.id', 'users.commission')
                ->where('selings.product_id', '=', 'ia_system_deposit')
                ->groupBy('users.id', 'users.commission')
                ->havingRaw('SUM(CASE WHEN selings.line = 0 OR selings.line = 1 THEN selings.sum * users.commission
            WHEN selings.line >= 2 AND selings.line <= 5 THEN selings.sum * users.commission / 10
            ELSE 0 END) >= 100000')
                ->get();
            $ids = [1,8475];
            foreach ($users as $user) {
                $ids[] = $user->id;
            }
            $users = User::whereIn('id', array_unique($ids))->get();

            $transactions = ArbDeposit::where('start', '!=', null)
                    ->where('is_active', true)
                    ->sum('amount') * 0.5 * PoolPercent::where('key', 'pool-arb-5')->first()->percent;

            $sum_pools = $transactions / count($users) ?? 0;
        }

        return $sum_pools;
    }

    public function index(): array
    {
        $user = auth()->user();

        $deposits = IaSystemDeposit::where('user_id', $user->id)->sum('amount');
        $balance = IaSystem::where('user_id', $user->id)->first();
        if(!$balance) {
            $balance = IaSystem::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        $response = [
            'sum_deposits' => $deposits,
            'balance' => $balance->balance,
            'percentage_of' => 1,
            'percentage_up_to' => 5,
        ];

        return $response;
    }
}
