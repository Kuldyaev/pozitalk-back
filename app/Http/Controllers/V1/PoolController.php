<?php

namespace App\Http\Controllers\V1;

use App\Models\Selling;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PoolController extends Controller
{
    public function pools(): JsonResponse
    {
        $user = Auth::user();

        $cachedData = Cache::get('pools_' . $user->id);
        if ($cachedData) {
            return response()->json($cachedData);
        }

        $usersBronze = User::where('commission', '>=', 0.5)->get()->pluck('id')->toArray();
        $countUsersBronze = count($usersBronze) - 4;

        $usersSilver = User::where('commission', '>=', 0.7)->get()->pluck('id')->toArray();
        $countUsersSilver = count($usersSilver) - 4;

        $usersGold = User::where('commission', '>=', 1)->get()->pluck('id')->toArray();
        $countUsersGold = count($usersGold) - 4;

        $plats = UsdtTransaction::where(function($query) {
        $query->where('product', 'platinum')
            ->orWhere('product', 'platinum_pay');
        })
        ->get();
        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }
        $platinumUsers = User::whereIn('id', $platinumUsers)->get()->pluck('id')->toArray();
        $countUsersPlatinum = count($platinumUsers) - 4;


        $usersFounder1 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 100000')
            ->get()->pluck('id')->toArray();
        $countUsersFounder1 = count($usersFounder1);

        $usersFounder2 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 250000')
            ->get()->pluck('id')->toArray();
        $countUsersFounder2 = count($usersFounder2);

        $usersFounder3 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 500000')
            ->get()->pluck('id')->toArray();
        $countUsersFounder3 = count($usersFounder3);

        $usersFounder4 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 1000000')
            ->get()->pluck('id')->toArray();
        $countUsersFounder4 = count($usersFounder4);

        $pools = [
            'pools' => [
                'bronze' => [
                    'count' => $countUsersBronze,
                    'participant' => in_array($user->id, $usersBronze)
                ],
                'silver' => [
                    'count' => $countUsersSilver,
                    'participant' => in_array($user->id, $usersSilver)
                ],
                'gold' => [
                    'count' => $countUsersGold,
                    'participant' => in_array($user->id, $usersGold)
                ],
                'platinum' => [
                    'count' => $countUsersPlatinum,
                    'participant' => in_array($user->id, $platinumUsers)
                ],
                'founder1' => [
                    'count' => $countUsersFounder1,
                    'participant' => in_array($user->id, $usersFounder1)
                ],
                'founder2' => [
                    'count' => $countUsersFounder2,
                    'participant' => in_array($user->id, $usersFounder2)
                ],
                'founder3' => [
                    'count' => $countUsersFounder3,
                    'participant' => in_array($user->id, $usersFounder3)
                ],
                'founder4' => [
                    'count' => $countUsersFounder4,
                    'participant' => in_array($user->id, $usersFounder4)
                ],
            ]
        ];

        Cache::put('pools_' . $user->id, $pools, Carbon::now()->addMinutes(5));

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $pools
        );
    }

    public function sellingStatistics(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:week,month,all',
        ]);

        $sellingStatistics = Selling::query();

        if ($validated['type'] == 'week') {
            $sellingStatistics->where('created_at', '>', now()->subWeek());
        } elseif ($validated['type'] == 'month') {
            $sellingStatistics->where('created_at', '>', now()->subMonth());
        }

        $sellingStatistics = $sellingStatistics->get();

        $sellingToday = UsdtTransaction::where('created_at', '>=', \Carbon\Carbon::now()->subDay())
            ->where(function($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum')
                    ->orWhere('product', 'account')
                    ->orWhere('product', 'dexnet')
                    ->orWhere('product', 'life_1')
                    ->orWhere('product', 'life_2')
                    ->orWhere('product', 'life_3')
                    ->orWhere('product', 'life_4')
                    ->orWhere('product', 'life_5')
                    ->orWhere('product', 'life_6')
                    ->orWhere('product', 'token_private');
            })
            ->where('address', '!=', 'admin')
            ->where('created_at', Carbon::now()->format('Y-m-d'))
            ->sum('sum_usd');

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'selling_today' => $sellingToday,
                'selling_statistic' => $sellingStatistics
            ]
        );
    }

    public function dayStatistic(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'date:Y-m-d',
        ]);

        $user = Auth::user();

        $usersBronze = User::where('commission', '>=', 0.5)->get()->pluck('id')->toArray();

        $usersSilver = User::where('commission', '>=', 0.7)->get()->pluck('id')->toArray();

        $usersGold = User::where('commission', '>=', 1)->get()->pluck('id')->toArray();

        $plats = UsdtTransaction::where(function($query) {
            $query->where('product', 'platinum')
                ->orWhere('product', 'platinum_pay');
        })
            ->get();
        $platinumUsers = [];
        foreach ($plats as $plat) {
            $platinumUsers[] = $plat->user_id;
        }
        $platinumUsers = User::whereIn('id', $platinumUsers)->get()->pluck('id')->toArray();

        $usersFounder1 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 100000')
            ->get()->pluck('id')->toArray();

        $usersFounder2 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 250000')
            ->get()->pluck('id')->toArray();

        $usersFounder3 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 500000')
            ->get()->pluck('id')->toArray();

        $usersFounder4 = DB::table('users')
            ->select('users.id', DB::raw('SUM(selings.sum) as total_sales'))
            ->join('selings', 'users.id', '=', 'selings.owner_id')
            ->where('selings.product_id', '!=', 'arb_deposit')
            ->groupBy('users.id')
            ->havingRaw('SUM(selings.sum) > 1000000')
            ->get()->pluck('id')->toArray();

        if(isset($validated['date'])) {
            $poolsDayStatistic = Selling::with('pools')
                ->where('date', $validated['date'])
                ->get();
        }
        else {
            $poolsDayStatistic = Selling::with('pools')
                ->orderByDesc('date')
                ->paginate(4);
        }

        foreach ($poolsDayStatistic as $value) {
            foreach ($value->pools as $pool) {
                if($pool->key == 'bronze') {
                    $pool->is_participant = in_array($user->id, $usersBronze);
                }
                elseif($pool->key == 'silver') {
                    $pool->is_participant = in_array($user->id, $usersSilver);
                }
                elseif($pool->key == 'gold') {
                    $pool->is_participant = in_array($user->id, $usersGold);
                }
                elseif($pool->key == 'platinum') {
                    $pool->is_participant = in_array($user->id, $platinumUsers);
                }
                elseif($pool->key == 'founder1') {
                    $pool->is_participant = in_array($user->id, $usersFounder1);
                }
                elseif($pool->key == 'founder2') {
                    $pool->is_participant = in_array($user->id, $usersFounder2);
                }
                elseif($pool->key == 'founder3') {
                    $pool->is_participant = in_array($user->id, $usersFounder3);
                }
                elseif($pool->key == 'founder4') {
                    $pool->is_participant = in_array($user->id, $usersFounder4);
                }
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'day_statistic' => $poolsDayStatistic
            ]
        );
    }
}
