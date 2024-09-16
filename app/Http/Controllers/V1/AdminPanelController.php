<?php

namespace App\Http\Controllers\V1;

use App\Actions\Round\NewGiverAction;
use App\Actions\Wallets\TicketReportAction;
use App\Actions\Wallets\TokenStackingReportAction;
use App\Actions\Wallets\TokenVestingReportAction;
use App\Http\Controllers\Exception;
use App\Models\Report;
use App\Models\ReportReferral;
use App\Models\Round;
use App\Models\RoundGiver;
use App\Models\RoundType;
use App\Models\Seling;
use App\Models\TicketReport;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBlock;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPanelController extends Controller
{
    public function ticketReports(Request $request)
    {
        $request->validate([
            'sortBy' => 'string',
            'sort' => 'string',
            'from' => 'string',
            'to' => 'string',
        ]);

        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        $query = TicketReport::orderBy($sort_by, $sort)
            ->paginate(15);

        foreach ($query as $item) {
            $query['user'] = User::find($item->user_id);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $statistic = [
                'minus' => TicketReport::where('type', '!=', 'payed_ticket')
                    ->where('type', '!=', 'gift_admin')
                    ->where('type', '!=', 'giver_pay_refund')
                    ->where('updated_at', '>=', $request->get('from'))
                    ->where('updated_at', '<=', $request->get('to'))
                    ->sum('count'),
                'plus' => TicketReport::where(function ($query2) {
                    $query2->where('type', 'payed_ticket');
                    $query2->where('type', 'gift_admin');
                    $query2->where('type', 'giver_pay_refund');
                })
                    ->sum('count'),
            ];
        } else {
            $statistic = [
                'minus' => TicketReport::where('type', '!=', 'payed_ticket')
                    ->where('type', '!=', 'gift_admin')
                    ->where('type', '!=', 'giver_pay_refund')
                    ->sum('count'),
                'plus' => TicketReport::where(function ($query2) {
                    $query2->where('type', 'payed_ticket');
                    $query2->where('type', 'gift_admin');
                    $query2->where('type', 'giver_pay_refund');
                })
                    ->sum('count'),
            ];
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'ticket_reports' => $query,
                'statistic' => $statistic,
            ]
        );
    }

    public function giftStatus(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'status' => 'required|string',
        ]);

        $user = User::findOrFail($request->get('user_id'));

        if ($request->get('status') == 'base') {
            $commission = 0.3;
            $usdt_sum = 0;
        } elseif ($request->get('status') == 'bronze') {
            $commission = 0.5;
            $usdt_sum = 200;
        } elseif ($request->get('status') == 'silver') {
            $commission = 0.7;
            $usdt_sum = 800;
        } elseif ($request->get('status') == 'gold') {
            $commission = 1;
            $usdt_sum = 2400;
        } elseif ($request->get('status') == 'platinum') {
            $commission = 1;
            $usdt_sum = 5000;
        } else {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный статус'],
                []
            );
        }

        $trans = UsdtTransaction::where('user_id', $user->id)
            ->where('address', 'admin')
            ->get();
        foreach ($trans as $item) {
            $item->delete();
        }

        $tr = new UsdtTransaction();
        $tr->user_id = $user->id;
        $tr->transaction_id = 'admin';
        $tr->sum_usd = $usdt_sum;
        $tr->address = 'admin';
        $tr->product = $request->get('status');
        $tr->date = Carbon::now();
        $tr->save();

        $user->commission = $commission;
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }

    public function giftTicket(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'count' => 'required|integer',
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $user->count_avatars += $request->get('count');
        $user->save();

        TicketReportAction::create($request->get('user_id'), $request->get('count'), 'gift_admin');

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }
    public function giftTokens(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'count' => 'required|integer',
            'type' => 'required|string',
        ]);

        $user = User::findOrFail($request->get('user_id'));

        if ($request->get('type') == 'stacking') {
            $user->token_stacking += $request->get('count');
            TokenStackingReportAction::create($request->get('user_id'), $request->get('count'), 'gift_admin');
        } elseif ($request->get('type') == 'vesting') {
            $user->token_vesting += $request->get('count');
            TokenVestingReportAction::create($request->get('user_id'), $request->get('count'), 'gift_admin');
        } else {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Неверный тип'],
                []
            );
        }
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }

    public function giftUsdShareholding(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'count' => 'required',
            'comment' => 'nullable|string',
            'is_private_com' => 'nullable|boolean'
        ]);

        $isPrivateCom = $request->has('is_private_com') ? $request->get('is_private_com') : false;

        $user = User::findOrFail($request->get('user_id'));
        $user->wallet += $request->get('count');
        $user->save();

        $rep = ReportReferral::create([
            'owner_id' => 1,
            'member_id' => $user->id,
            'sum' => $request->get('count'),
            'type' => $isPrivateCom ? 'private_com' : 'admin',
            'comment' => $request->get('comment'),
            'data' => [
                'balance' => $user->wallet,
            ]
        ]);

        $rep->type = $isPrivateCom ? 'private_com' : 'admin';
        $rep->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }

    public function giftAdmin(Request $request)
    {
        $request->validate([
            'round_id' => 'required|integer',
        ]);

        $round = Round::with([
            'roundGivers' => function ($query) {
                $query->where('status_id', '!=', 1);
                $query->where('status_id', '!=', 5);
                $query->where('status_id', '!=', 6);
                $query->where('status_id', '!=', 7);
                $query->where('status_id', '!=', 8);
            }
        ])
            ->find($request->get('round_id'));

        if (!$round) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Раунда нет'],
                []
            );
        }

        if (count($round->roundGivers) > 2) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Уже есть 3 дарителя'],
                []
            );
        }

        $gift_user = User::with('accounts')->find(8320);
        foreach ($gift_user->accounts as $account) {
            if ($round->round_type_id == $account->next_round && $account->role_id == 1) {
                $account->role_id = 2;
                $account->save();

                $gift_giver = RoundGiver::create([
                    'round_id' => $round->id,
                    'account_id' => $account->id,
                    'status_id' => 2,
                    'round_type_id' => $round->round_type_id,
                    'start' => Carbon::now(),
                ]);

                break;
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'round' => $round
            ]
        );
    }

    public function userCode(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $user = User::find($request->get('user_id'));
        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Пользователя нет'],
                []
            );
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'code' => $user['code']
            ]
        );
    }

    public function priorityAccount(Request $request)
    {
        $request->validate([
            'round_id' => 'required|integer',
        ]);

        $round = Round::find($request->get('round_id'));
        if (!$round) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Раунда нет'],
                []
            );
        }
        $round->priority = $round->priority == false;
        $round->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'round' => $round
            ]
        );
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $user = User::find($request->get('user_id'));
        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Пользователя нет'],
                []
            );
        }

        $user->blocked = null;
        $user->blocked_message = null;
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }

    public function blocked(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|integer',
            'message' => 'string',
        ]);

        $user = User::find($request->get('user_id'));
        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Пользователя нет'],
                []
            );
        }

        $type_blocked = [
            1 => Carbon::now()->addDays(1),
            2 => Carbon::now()->addDays(3),
            3 => Carbon::now()->addDays(7),
            4 => Carbon::now()->addYears(10),
        ];

        $block_user = new UserBlock;
        $block_user->user_id = $user->id;
        $block_user->time = $request->get('type');
        $block_user->message = $request->get('message');
        $block_user->save();

        $user->blocked = $type_blocked[$request->get('type')];
        $user->blocked_message = $request->get('message');
        $user->save();

        if ($request->get('type') == 4) {
            $user_refs = User::where('referal_id', $user->id)->get();
            foreach ($user_refs as $user_ref) {
                $user_ref->referal_id = $user->referal_id;
                $user_ref->save();
            }
        }

        $accounts = UserAccount::where('user_id', $user->id)->where('available', true)->get();
        foreach ($accounts as $account) {
            if ($account->role_id == 2) {
                $round_giver = RoundGiver::where('account_id', $account->id)->orderBy('id', 'desc')->first();
                $round_giver->status_id = 6;
                $round_giver->save();

                $account->role_id = 1;
                $account->save();

                $us = $round_giver->round->account->user;
                $us->count_avatars += 1;
                $us->save();

                TicketReportAction::create($us->id, 1, 'giver_pay_refund');

                if ($round_giver->is_priority && $round_giver->round_id != null) {
                    $round = Round::find($round_giver->round_id);
                    $round->priority = true;
                    $round->save();
                }
            } elseif ($account->role_id == 3) {
                $round = Round::where('account_id', $account->id)->where('active', true)->first();
                if ($round) {
                    $round->active = false;
                    $round->save();

                    $new_round = new Round();
                    $new_round->account_id = $round->account_id;
                    $new_round->round_type_id = $round->round_type_id;
                    $new_round->active = true;
                    $new_round->verification_code = rand(1001, 9999);
                    $new_round->price = $round->price;
                    $new_round->save();

                    $givers = RoundGiver::where('round_id', $round->id)->where(function ($q) {
                        $q->where('status_id', 2)
                            ->orWhere('status_id', 3)
                            ->orWhere('status_id', 8);
                    })
                        ->get();

                    foreach ($givers as $giver) {
                        if ($giver->status_id == 2 || $giver->status_id == 8 || $giver->status_id == 3) {

                            $giver->round_id = $new_round->id;
                            $giver->status_id = 7;
                            $giver->save();

                            if ($giver->account->next_round != 1) {
                                $need_count_givers = RoundType::find($giver->account->next_round)->count_givers;

                                $round = Round::with([
                                    'roundGivers' => function ($query) {
                                        $query->where('status_id', '!=', 1);
                                        $query->where('status_id', '!=', 5);
                                        $query->where('status_id', '!=', 6);
                                        $query->where('status_id', '!=', 7);
                                    }
                                ])
                                    ->where('active', true)
                                    ->where('round_type_id', $giver->account->next_round)
                                    ->where('account_id', '<', 5)
                                    ->first();

                                if (!$round || (isset($round->roundGivers) && count($round->roundGivers) >= $need_count_givers)) {
                                    $rounds = Round::with([
                                        'roundGivers' => function ($query) {
                                            $query->where('status_id', '!=', 1);
                                            $query->where('status_id', '!=', 5);
                                            $query->where('status_id', '!=', 6);
                                            $query->where('status_id', '!=', 7);
                                        }
                                    ])
                                        ->where('active', true)
                                        ->where('priority', true)
                                        ->where('round_type_id', $giver->account->next_round)
                                        ->get();
                                }

                                if (!$round || (isset($round->roundGivers) && count($round->roundGivers) >= $need_count_givers) && !isset($rounds)) {
                                    $rounds = Round::with([
                                        'roundGivers' => function ($query) {
                                            $query->where('status_id', '!=', 1);
                                            $query->where('status_id', '!=', 5);
                                            $query->where('status_id', '!=', 6);
                                            $query->where('status_id', '!=', 7);
                                        }
                                    ])
                                        ->where('active', true)
                                        ->where('round_type_id', $giver->account->next_round)
                                        ->get();
                                } else {
                                    $rounds[] = $round;
                                }

                                $round_id = null;
                                $receiver = null;
                                foreach ($rounds as $round) {
                                    if (
                                        count($round->roundGivers) < $need_count_givers
                                        && $giver->account->user_id !== $round->account->user_id
                                        && $round->account->user->updated_at >= Carbon::now()->subWeeks(4)->toDateTimeString()
                                    ) {
                                        $round_id = $round->id;
                                        $receiver = $round->account_id;

                                        try {
                                            $tg_id = $round->account->user->telegram_id;
                                            if ($tg_id != null) {
                                                $text = "У вас появился даритель в целевой очереди, в аккаунте #" . $round->account->number . "
                            Для того, чтобы увидеть дарителя в кабинете необходимо активировать тикет на получение этого подарка.
                            Зайдите в личный кабинет, следуйте инструкции в очереди.
                            Обратите внимание на время, если не успеете активировать тикет на получение подарка, то придется ждать другого дарителя";
                                                $keyboard = [
                                                    'text' => 'Перейти на сайт',
                                                    'url' => 'https://vbalance.net/auth',
                                                ];
                                                $this->sendTgMessage($tg_id, $text, $keyboard);
                                            }
                                        } catch (Exception $e) {
                                        }

                                        $cel = true;

                                        $round = $round;
                                        break;
                                    }
                                }
                            } else {
                                $round_id = null;
                                $receiver = null;
                                $cel = false;
                            }
                            if (!isset($cel))
                                $cel = false;

                            NewGiverAction::store($round_id, $giver->account, $cel);
                        }
                    }

                    $account->save();
                }
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user,
            ]
        );
    }

    public function changeRef(Request $request)
    {
        $request->validate([
            'new_ref' => 'required|integer',
            'user' => 'required|integer',
        ]);

        $user = User::find($request->get('user'));
        if (!$user) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Пользователя нет'],
                []
            );
        }

        $new_ref = User::find($request->get('new_ref'));
        if (!$new_ref) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['Реферала нет'],
                []
            );
        }

        $user->referal_id = $request->get('new_ref');
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

    public function user($id)
    {
        $user = User::find($id);
        $user['blocked_list'] = UserBlock::where('user_id', $user->id)->get();

        $user['ticket_statistic'] = TicketReport::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $user['package'] = UsdtTransaction::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum');
            })
            ->orderBy('id', 'desc')
            ->first()->product ?? 'base';

        $user['history_wallet'] = ReportReferral::where('member_id', $user->id)->orderBy('created_at', 'desc')->get();

        $childrens = User::where('referal_id', $user->id)->paginate(10);
        //        if ($childrens) {
//            foreach($childrens as $k=>$children) {
//                $total = 0;
//                $straight_total = 0;
//                if($childrens) {
//                    $ids = [];
//                    foreach($childrens as $u) {
//                        $ids[] = $u->id;
//                    }
//                    $straight_total += count($ids);
//
//                    while (true) {
//                        if($ids) {
//                            $users = User::whereIn('referal_id', $ids)->get();
//                            if ($users) {
//                                $ids = [];
//                                foreach ($users as $u) {
//                                    $ids[] = $u->id;
//                                }
//                                $total += count($ids);
//                            }
//                            else
//                                break;
//                        }
//                        else
//                            break;
//                    }
//                }
//
//                $childrens[$k]['totals'] = [
//                    'straight' => $straight_total,
//                    'total' => $total,
//                ];
//            }
//        }

        $user['referal_login'] = User::find($user->referal_id)->login ?? null;

        $user['selling'] = Seling::where('member_id', $user->id)->orderByDesc('date')->get();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user,
                'hierarchy' => $childrens ?? null,
            ]
        );
    }

    public function userUpdate($id, Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'telegram_name' => 'required|string',
            'telegram_id' => 'required|integer',
            'phone' => 'required|integer',
        ]);

        $user = User::find($id);
        $user->login = $request->get('login');
        $user->telegram_name = $request->get('telegram_name');
        $user->telegram_id = $request->get('telegram_id');
        $user->phone = $request->get('phone');
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

    public function account($id)
    {
        $account = UserAccount::with([
            'rounds' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'rounds.roundGivers',
            'givers' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
            'givers.account.user',
            'givers.round.account.user',
            'rounds.roundGivers.account.user',
            'user'
        ])
            ->find($id);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'account' => $account,
            ]
        );
    }

    public function allUsers(Request $request)
    {
        $request->validate([
            'searchBy' => 'string',
            'query' => 'string',
            'sortBy' => 'string',
            'sort' => 'string',
            'status' => 'nullable|string|in:bronze,silver,gold,platinum'
        ]);

        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        if ($request->get('status') == 'bronze') {
            $query = User::where('commission', 0.5)->orderBy($sort_by, $sort);
        } elseif ($request->get('status') == 'silver') {
            $query = User::where('commission', 0.7)->orderBy($sort_by, $sort);
        } elseif ($request->get('status') == 'gold') {
            $goldUsers = UsdtTransaction::where(function ($query) {
                $query->where('product', 'gold')
                    ->orWhere('product', 'gold_pay');
            })
                ->pluck('user_id')
                ->toArray();

            $query = User::whereIn('id', $goldUsers)->orderBy($sort_by, $sort);
        } elseif ($request->get('status') == 'platinum') {
            $platinumUsers = UsdtTransaction::where(function ($query) {
                $query->where('product', 'platinum')
                    ->orWhere('product', 'platinum_pay');
            })
                ->pluck('user_id')
                ->toArray();

            $query = User::whereIn('id', $platinumUsers)->orderBy($sort_by, $sort);
        } else {
            $query = User::orderBy($sort_by, $sort);
        }

        if ($request->get('searchBy') === 'phone') {
            $phone = $request->get('query');
            $query->where('phone', 'like', "%$phone%");
        } elseif ($request->get('searchBy') === 'telegram_name') {
            $telegram_name = $request->get('query');
            $query->where('telegram_name', 'like', "%$telegram_name%");
        } elseif ($request->get('searchBy') === 'login') {
            $login = $request->get('query');
            $query->where('login', 'like', "%$login%");
        }

        $users = $query->paginate(15);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'users' => $users,
            ]
        );
    }

    public function allUsersPriority(Request $request)
    {
        $request->validate([
            'searchBy' => 'string',
            'query' => 'string',
            'sortBy' => 'string',
            'sort' => 'string',
        ]);

        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        $rounds = Round::with([
            'account.user',
        ])
            ->where('active', true)
            ->where('priority', true)
            ->get();

        $ids = [];
        if ($rounds) {
            foreach ($rounds as $round) {
                $ids[] = $round->account->user->id;
            }
        }

        $query = User::WhereIn('id', $ids)->orderBy($sort_by, $sort);

        if ($request->get('searchBy') === 'phone') {
            $phone = $request->get('query');
            $query->where('phone', 'like', "%$phone%");
        } elseif ($request->get('searchBy') === 'telegram_name') {
            $telegram_name = $request->get('query');
            $query->where('telegram_name', 'like', "%$telegram_name%");
        } elseif ($request->get('searchBy') === 'login') {
            $login = $request->get('query');
            $query->where('login', 'like', "%$login%");
        }

        $users = $query->paginate(15);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'users' => $users,
            ]
        );
    }

    public function allUsersGifts(Request $request)
    {
        $request->validate([
            'searchBy' => 'string',
            'query' => 'string',
            'sortBy' => 'string',
            'sort' => 'string',
        ]);
        //        $givers = RoundGiver::with([
//                'round.account.user',
//            ])
//            ->where('status_id', 4)
//            ->orederBy('updated_at', 'desc')
//            ->paginate(15);



        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        $givers = RoundGiver::with([
            'round.account.user',
        ])
            ->where('status_id', 4)
            ->orderBy($sort_by, $sort)
            ->paginate(15);

        //        if ($request->get('searchBy') === 'phone') {
//            $phone = $request->get('query');
//            $query->where('phone', 'like', "%$phone%");
//        }
//        elseif ($request->get('searchBy') === 'telegram_name') {
//            $telegram_name = $request->get('query');
//            $query->where('telegram_name', 'like', "%$telegram_name%");
//        }
//        elseif ($request->get('searchBy') === 'login') {
//            $login = $request->get('query');
//            $query->where('login', 'like', "%$login%");
//        }

        //        $givers = $query->paginate(15);




        $result = [];
        foreach ($givers as $giver) {
            $result[] = [
                'id' => $giver->round->account->user->id,
                'login' => $giver->round->account->user->login,
                'phone' => $giver->round->account->user->phone,
                'telegram_name' => $giver->round->account->user->telegram_name,
                'round_type_id' => $giver->round->round_type_id,
                'updated_at' => $giver->updated_at,
                'account' => $giver->round->account,
            ];
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'users' => $result,
                'total' => $givers->total(),
            ]
        );
    }

    public function statistic(Request $request)
    {
        $request->validate([
            'from' => 'string',
            'to' => 'string',
        ]);

        $from = $request->get('from');
        $to = $request->get('to');

        if ($request->filled('from') && $request->filled('to')) {
            $request = [
                'users' => User::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->count(),
                'accounts' => UserAccount::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->where('available', 1)->count(),
                'user_accounts' => UserAccount::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->count(),
                'inactives' => UserAccount::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->where('role_id', 1)->count(),
                'givers' => UserAccount::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->where('role_id', 2)->count(),
                'recipients' => UserAccount::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->where('role_id', 3)->count(),
                'academy' => UsdtTransaction::where('created_at', '>=', $request->get('from'))->where('created_at', '<=', $request->get('to'))->where('product', 'academy_3')->count(),
            ];

            $rounds = Round::with([
                'roundGivers' => function ($query) {
                    $query->where('status_id', 4);
                }
            ])
                ->where('updated_at', '>=', $from)
                ->where('updated_at', '<=', $to)
                ->where('active', false)
                ->get();

            $bronze = UsdtTransaction::where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->where('product', 'bronze')
                ->count();
            $silver = UsdtTransaction::where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->where('product', 'silver')
                ->count();
            $gold = UsdtTransaction::where('created_at', '>=', $from)
                ->where('created_at', '<=', $to)
                ->where('product', 'gold')
                ->count();
        } else {
            $request = [
                'users' => User::count(),
                'accounts' => UserAccount::where('available', 1)->count(),
                'user_accounts' => UserAccount::count(),
                'inactives' => UserAccount::where('role_id', 1)->count(),
                'givers' => UserAccount::where('role_id', 2)->count(),
                'recipients' => UserAccount::where('role_id', 3)->count(),
                'academy' => UsdtTransaction::where('product', 'academy_3')->count(),
            ];

            $rounds = Round::with([
                'roundGivers' => function ($query) {
                    $query->where('status_id', 4);
                }
            ])
                ->where('active', false)
                ->get();

            $bronze = UsdtTransaction::where('product', 'bronze')->count();
            $silver = UsdtTransaction::where('product', 'silver')->count();
            $gold = UsdtTransaction::where('product', 'gold')->count();
        }


        $one = 0;
        $two = 0;
        $three = 0;
        $four = 0;
        foreach ($rounds as $round) {
            if ($round->round_type_id == 1) {
                $one = $one + count($round->roundGivers);
            }
            if ($round->round_type_id == 2) {
                $two = $two + count($round->roundGivers);
            }
            if ($round->round_type_id == 3) {
                $three = $three + count($round->roundGivers);
            }
            if ($round->round_type_id == 4) {
                $four = $four + count($round->roundGivers);
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'statistic' => $request,
                'gifts' => [
                    '50' => $one,
                    '100' => $two,
                    '200' => $three,
                    '400' => $four,
                ],
                'packages' => [
                    'bronze' => $bronze,
                    'silver' => $silver,
                    'gold' => $gold,
                ]
            ]
        );
    }

    public function cancelGiverAdmin(Request $request)
    {
        $request->validate([
            'giver_id' => ['required', 'integer'],
        ]);

        $giver = RoundGiver::find($request->get('giver_id'));
        if ($giver->status_id == 5) {
            return ResponseService::sendJsonResponse(
                false,
                303,
                [],
                []
            );
        }

        if ($giver->status_id == 2 || $giver->status_id == 3) {
            $us = $giver->round->account->user;
            $us->count_avatars += 1;
            $us->save();

            TicketReportAction::create($us->id, 1, 'giver_pay_refund');
        }

        $giver->status_id = 5;
        $giver->save();

        $giver_account = UserAccount::find($giver->account_id);
        $giver_account->role_id = 1;
        $giver_account->save();

        //        $giver_pay = RoundGiverPay::where('giver_id', $giver->id)->orderBy('id', 'desc')->first();
//        if(isset($giver_pay)) {
//            RoundGiverPay::created([
//                'round_id' => $giver_pay->round_id,
//                'is_payed' => $giver_pay->is_payed,
//            ]);
//            $giver_pay->delete();
//        }

        // отчет
        if (isset($giver->round)) {
            $report = new Report();
            $report->to_id = $giver->round->account_id;
            $report->from_id = $giver->account_id;
            $report->round_id = $giver->round_id;
            $report->amount = 0;
            $report->successful = false;
            $report->save();
        }

        if ($giver->is_priority && $giver->round_id != null) {
            $round = Round::find($giver->round_id);
            $round->priority = true;
            $round->save();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'round_giver' => $giver ?? '',
            ]
        );
    }

    public function isActive()
    {
        $users = User::where('is_active', true)->paginate(15);

        foreach ($users as $user) {
            $accounts = UserAccount::where('user_id', $user->id)->get()->pluck('id')->toArray();

            $condition1 = RoundGiver::whereIn('account_id', $accounts)
                ->where('status_id', 4)
                ->where('round_type_id', 1)
                ->count() > 0;

            $rounds = Round::whereIn('account_id', $accounts)
                ->where('active', true)
                ->where('round_type_id', 1)
                ->get()->pluck('id')->toArray();

            $condition2 = RoundGiver::whereIn('round_id', $rounds)
                ->where('status_id', 4)
                ->count() > 0;

            $user['condition'] = true;
            if ($condition1) {
                $user['condition'] = false;
            }
            if (!$condition2) {
                $user['condition'] = false;
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'users' => $users,
            ]
        );
    }

    public function isProcessed(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $user->is_processed = true;
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            []
        );
    }

    public function adminCharges()
    {

        $reports = ReportReferral::where('type', 'admin')->orderBy('id', 'desc')->paginate(15);
        foreach ($reports as $report) {
            $report['user'] = User::find($report->member_id);
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $reports
        );
    }

    public function changeFounderStatus(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'founder_status' => 'required|integer|in:0,1,2,3,4',
        ]);

        $user = User::findOrFail($request->get('user_id'));
        $user->founder_status = $request->get('founder_status');
        $user->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'user' => $user
            ]
        );
    }
}
