<?php

namespace App\Http\Controllers\V1;

use App\Actions\Round\NewGiverAction;
use App\Models\Round;
use App\Models\RoundGiver;
use App\Models\RoundType;
use App\Models\Seling;
use App\Models\TokenPrivateReport;
use App\Models\TokenRate;
use App\Models\TokenStackingReport;
use App\Models\TokenVestingReport;
use App\Models\UsdtTransaction;
use App\Models\UsdtWallet;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\Response\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function shortInfoReferrals(): JsonResponse
    {
        $user = Auth::user();

        $cachedData = Cache::get('user_referrals_short_info_' . $user->id);
        if ($cachedData) {
            return response()->json($cachedData);
        }

        $childrens = User::where('referal_id', $user->id)->get();
        $total = 0;
        $straight_total = 0;
        $five_lines_total = 0;
        if($childrens) {
            $ids = [];
            foreach($childrens as $u) {
                $ids[] = $u->id;
            }
            $total = count($ids);
            $straight_total += count($ids);
            $five_lines_total += count($ids);

            $i = 1;
            while (true) {
                if($ids) {
                    $users = User::whereIn('referal_id', $ids)->get();
                    if ($users) {
                        $ids = [];
                        foreach ($users as $u) {
                            $ids[] = $u->id;
                        }
                        $total += count($ids);
                        if($i <= 4) {
                            $five_lines_total += count($ids);
                        }
                        $i++;
                    }
                    else
                        break;
                }
                else
                    break;
            }
        }

        $response = [
            'referrals' => [
                'total' => $total,
                'first_line' => $straight_total,
                'five_lines' => $five_lines_total,
            ]
        ];

        Cache::put('user_referrals_short_info_' . $user->id, $response, Carbon::now()->addMinutes(30));

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function cryptoWallets(Request $request): JsonResponse
    {
        $user = Auth::user();

        $request->validate([
            'wallet_address' => 'required|unique:user_crypto_wallets,wallet_address,NULL,id,user_id,' . $user->id
        ]);

        $user->cryptoWallets()->update([
            'is_active' => false
        ]);

        $user->cryptoWallets()->create([
            'wallet_address' => $request->get('wallet_address'),
            'is_active' => true
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function testResult(Request $request) {
        $request->validate([
            'test_result' => 'required|integer',
        ]);
        $user = Auth::user();
        $user->test_result = $request->get('test_result');
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'message' => 'Test result saved successfully'
            ]
        );
    }

    public function getHierarchy() {
        $user = Auth::user();

        $cachedData = Cache::get('user_hierarchy_' . $user->id);
        if ($cachedData) {
            return response()->json($cachedData);
        }

        $own_users = User::where('referal_id', Auth::user()->id)->get();
        $total=0;

        $lines = array();
        if($own_users)
        {
            $ids=array();
            foreach($own_users as $u)
            {
                $ids[]=$u->id;
            }
            $total += count($ids);

            $lines[] = count($ids);
            while (true)
            {
                if($ids)
                {
                    $users = User::WhereIn('referal_id', $ids)->get();

                    if ($users) {
                        $ids = array();
                        foreach ($users as $u) {
                            $ids[] = $u->id;
                        }
                        $total += count($ids);

                        $lines[] = count($ids);
                    }
                    else
                        break;
                }
                else
                    break;
            }
        }

        $hierarchy = [
            'own_users' => count($own_users),
            'total_users' => $total,
        ];
      
        $respons = [
            'hierarchy' => $hierarchy ?? '',
            'lines' => $lines,
        ];

        Cache::put('user_hierarchy_' . $user->id, $respons, Carbon::now()->addMinutes(10));

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $respons
        );
    }

    public function getAccount() {
        $round_types = RoundType::where('queue', Auth::user()->active_queue)->get();
        foreach ($round_types as $round_type) {
            $account = UserAccount::where('user_id', Auth::user()->id)
                ->where('available', true)
                ->where('active', true)
                ->where('next_round', $round_type->id)
                ->first();
            if($account)
                break;
        }

        if($account) {
            if($account->role_id == 3) {
                $round = Round::with(['roundGivers' => function($query) {
                    $query->where('status_id', '!=', 1);
                    $query->where('status_id', '!=', 5);
                    $query->where('status_id', '!=', 6);
                    $query->where('status_id', '!=', 7);
                },
                    'roundGivers.account.user'])
                    ->where('account_id', $account->id)
                    ->where('active', true)
                    ->orderBy('id', 'desc')
                    ->first();

                if(!$round) {
                    $price = $account->roundType->price;

                    $round = new Round();
                    $round->account_id = $account->id;
                    $round->round_type_id = $account->next_round;
                    $round->active = true;
                    $round->verification_code = rand(1001, 9999);
                    $round->price = $price;
                    $round->save();
                }

//                $pays = RoundGiverPay::where('round_id', $round->id)->get();
//                $i = 0;
//                foreach ($pays as $pay) {
//                    if($pay->giver_id == null) {
//                        if(isset($round->roundGivers[$i])) {
//                            $pay->giver_id = $round->roundGivers[$i]->id;
//                            $pay->save();
//                        }
//                    }
//
//                    if($pay->is_payed == true && $round->roundGivers[$i]->status_id == 8) {
//                        $giv = $round->roundGivers[$i];
//                        $giv->status_id = 2;
//                        $giv->save();
//                    }
//                }

                foreach ($round->roundGivers as $round_giver) {
                    if($round_giver->status_id == 8) {
                        unset($round_giver->account);
                    }
                }
            }

            if($account->role_id == 2) {
                $round_giver = RoundGiver::where('account_id', $account->id)
                    ->where('status_id', '!=', 5)
                    ->where('status_id', '!=', 6)
                    ->where('status_id', '!=', 7)
                    ->orderBy('id', 'desc')
                    ->first();

                if(!$round_giver) {
                    $round_giver = NewGiverAction::store(null, $account);
                }

                if($round_giver->round_id != null && $round_giver->status_id != 8) {
                    $receiver = $round_giver->round->account->user;
                    $round = $round_giver->round;
                    $round_giver_now = $round_giver;
                } else {
                    $round_giver_now = $round_giver;
                }
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'account' => $account,
                'rounds' => $round ?? '',
                'receiver' => $receiver ?? '',
                'round_giver' => $round_giver_now ?? '',
                'hierarchy' => $hierarchy ?? ''
            ]
        );
    }

    public function managerAccount() {
        $user = Auth::user();
        $statistics = array();

        $round_types = RoundType::select('id', 'queue')->get()->groupBy('queue');
        $queues = [1, 2, 3, 4];

        foreach ($queues as $queue) {
            $queueTypes = $round_types->get($queue, collect())->pluck('id')->toArray();
            $queueCount = UserAccount::where('user_id', $user->id)
                ->where('available', true)
                ->whereIn('next_round', $queueTypes)
                ->count();
            $statistics['queue_' . $queue] = $queueCount;
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'available_avatars' => $user->count_avatars,
                'statistics' => $statistics,
            ]
        );
    }

    public function addAccount(Request $request) {
        $request->validate([
            'queue' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        if($request->get('queue') != 1) {
            if($user->count_avatars < 1)
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    [],
                    [
                        'message' => 'У вас нет доступных аватаров'
                    ]
                );
        }

        $round_types = RoundType::where('queue', $request->get('queue'))->get();
        $ids = [];
        $i = 0;
        foreach ($round_types as $round_type) {
            if($i == 0){
                $q = $round_type->id;
            }
            $ids[] = $round_type->id;

            $i++;
        }

        $accounts = UserAccount::where('user_id', $user->id)
            ->where('available', true)
            ->whereIn('next_round', $ids)
            ->get();

        $count_acs = count($accounts);

        if($request->get('queue') != 1) {
            if ($count_acs >= 3)
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    [],
                    [
                        'message' => 'Можно активировать только 3 аватара в одной очереди'
                    ]
                );

            $user->count_avatars = $user->count_avatars - 1;
            $user->save();
        }
        else {
            if ($count_acs >= 12)
                return ResponseService::sendJsonResponse(
                    false,
                    400,
                    [],
                    [
                        'message' => 'Можно активировать только 12 аватара в базовой очереди'
                    ]
                );
        }

        foreach($accounts as $account) {
            $account->active = false;
            $account->save();
        }

        $user_account = new UserAccount();
        $user_account->user_id = $user->id;
        $user_account->active = 1;
        $user_account->role_id = 1;
        $user_account->next_round = $q;
        $user_account->circle = 1;
        $user_account->number = $count_acs + 1;
        $user_account->save();


        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'account' => $user_account,
            ]
        );
    }

    public function changeAccount(Request $request) {
        $request->validate([
            'account_id' => ['required', 'integer'],
        ]);

        $account = UserAccount::find($request->get('account_id'));

        $round_types = RoundType::where('queue', $account->roundType->queue)
            ->get();
        $ids = [];
        foreach ($round_types as $round_type) {
            $ids[] = $round_type->id;
        }

        $accounts = UserAccount::where('user_id', Auth::user()->id)
            ->where('available', true)
            ->whereIn('next_round', $ids)
            ->get();

        foreach($accounts as $account) {
            $account->active = $account->id == $request->get('account_id');
            $account->save();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'accounts' => $accounts,
            ]
        );
    }

    public function settings(Request $request) {
        $request->validate([
            'login' => ['required', 'string', 'min:3'],
            'message' => ['string', 'max:250'],
        ]);

        $user = Auth::user();
        $user->login = $request->get('login');
        $user->message = $request->get('message') ?? '';
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user,
            ]
        );
    }

    public function walletGet(Request $request) {
        $request->validate([
            'product' => ['string'],
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

    public function walletPost(Request $request) {
        $request->validate([
            'product' => ['string'],
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

    public function payFromBalance(Request $request) {
        $request->validate([
            'sum_usd' => ['integer'],
            'product' => ['string'],
        ]);

        $user = Auth::user();

        if($request->get('sum_usd') > $user->wallet) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                [],
                [
                    'Недостаточно средств на балансе'
                ]
            );
        }

        UsdtTransaction::transactionPayed($user->id, $request->get('product'), $request->get('sum_usd'), true);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'Успешно'
            ]
        );
    }

    public function showWelcome() {
        $user = User::find(Auth::user()->id);
        $user->show_welcome = true;
        $user->save();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function changeActiveQueue(Request $request) {
        $request->validate([
            'queue' => ['required', 'integer'],
        ]);

        $user = Auth::user();

        $round_types = RoundType::where('queue', $request->get('queue'))->get();
        $ids = [];
        foreach ($round_types as $type) {
            $ids[] = $type->id;
        }

        $accounts = UserAccount::where('user_id', Auth::user()->id)
            ->where('available', true)
            ->WhereIn('next_round', $ids)
            ->get();

        $user->active_queue = $request->get('queue');
        $user->save();

        if($accounts) {
            foreach($accounts as $key => $account) {
                if($key == 0) {
                    $account->active = true;
                    $account->save();
                }
                else {
                    $account->active = false;
                    $account->save();
                }
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'accounts' => $accounts
            ]
        );
    }

    public function tokenomics() {
        $user = Auth::user();
        $rate = TokenRate::first();

        $history_stacking = TokenStackingReport::where('user_id', $user->id)->orderByDesc('created_at')->get()->values()->all();
        $history_vesting = TokenVestingReport::where('user_id', $user->id)->orderByDesc('created_at')->get()->values()->all();
        $collection = array_merge($history_stacking, $history_vesting);

        $response = [
            'banner' => [
                'buy_count_all' => TokenPrivateReport::sum('count') / (1000/$rate->private_rate),
                'buy_count_user' => TokenPrivateReport::where('user_id', $user->id)->sum('count') / (1000/$rate->private_rate),
                'count_private_tokens' => $user->token_private,
                'usd_from_private_tokens' => $user->token_private / (1/$rate->private_rate),
                'private_tokens_rate' => $rate->private_rate,
            ],
            'balance' => [
                'wallet' => [
                    'tokens' => $user->token_stacking + $user->token_vesting,
                    'tokens_in_usd' => ($user->token_stacking + $user->token_vesting) / (1/$rate->classic_rate),
                    'token_classic_rate' => $rate->classic_rate,
                    'history' => $collection,
                ],
                'stacking' => [
                    'in_stacking' => 0,
                    'available_in_vesting' => $user->token_stacking,
                ],
                'vesting' => [
                    'in_vesting' => 0,
                    'available_in_vesting' => $user->token_vesting,
                ],
                'how_to_get' => [
                    'base' => TokenVestingReport::where('user_id', $user->id)->where('type', 'gift_base')->count(),
                    'vacation' => TokenVestingReport::where('user_id', $user->id)->where('type', 'gift_vacation')->count(),
                    'car' => TokenVestingReport::where('user_id', $user->id)->where('type', 'gift_car')->count(),
                ]
            ]
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }

    public function isActive() {
        $user = Auth::user();
        $user->is_active = true;
        $user->save();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function lifeList() {
        $user = Auth::user();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'lists' => [
                    'life_1' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_1')->first(),
                    'life_2' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_2')->first(),
                    'life_3' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_3')->first(),
                    'life_4' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_4')->first(),
                    'life_5' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_5')->first(),
                    'life_6' => (bool) UsdtTransaction::where('user_id', $user->id)->where('product', 'life_6')->first(),
                ]
            ]
        );
    }

    public function selings(Request $request) {
        $request->validate([
            'type' => ['required', 'string', 'in:all,my'],
        ]);

        $user = Auth::user();

        if($request->get('type') == 'my') {
            $selings = Seling::where('member_id', $user->id)
                ->where('line', 0)
                ->where('product_id', '!=', 'arb_deposit')
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();

            $total = Seling::where('member_id', $user->id)
                ->where('line', 0)
                ->where('product_id', '!=', 'arb_deposit')
                ->sum('sum');
        }
        elseif ($request->get('type') == 'all') {
            $selings = Seling::where('member_id', $user->id)
                ->where('line', '!=', 0)
                ->where('product_id', '!=', 'arb_deposit')
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();

            $total = Seling::where('member_id', $user->id)
                ->where('line', '!=', 0)
                ->where('product_id', '!=', 'arb_deposit')
                ->sum('sum');

            $referrals = DB::table('users')
                ->join('selings', 'users.id', '=', 'selings.member_id')
                ->where('users.referal_id', $user->id)
                ->where('selings.product_id', '!=', 'arb_deposit')
                ->select('users.telegram_name', 'users.login', DB::raw('SUM(selings.sum) as total_sales'))
                ->groupBy('users.id', 'users.telegram_name', 'users.login')
                ->orderByDesc('total_sales')
                ->take(100)
                ->get();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'total' => $total,
                'history' => $selings,
                'referrals' => $referrals ?? null,
            ]
        );
    }
}
