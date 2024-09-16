<?php

namespace App\Http\Controllers\V1;

use App\Actions\Round\NewGiverAction;
use App\Actions\Wallets\TicketReportAction;
use App\Actions\Wallets\TokenVestingReportAction;
use App\Models\Report;
use App\Models\Round;
use App\Models\RoundGiver;
use App\Models\RoundType;
use App\Models\TokenVestingReport;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoundController extends Controller
{
    public function servTime() {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [Carbon::now()]
        );
    }

    public function cancelGiver(Request $request) {
        $request->validate([
            'giver_id' => ['required', 'integer'],
        ]);

        $giver = RoundGiver::find($request->get('giver_id'));
        if($giver->status_id == 5) {
            return ResponseService::sendJsonResponse(
                false,
                303,
                [],
                []
            );
        }

        if($giver->status_id == 2 || $giver->status_id == 3 || $giver->status_id == 4) {
            $user = Auth::user();
            $user->count_avatars += 1;
            $user->save();

            TicketReportAction::create($user->id, 1, 'giver_pay_refund');
        }

        $giver->status_id = 5;
        $giver->save();

        $giver_account = UserAccount::find($giver->account_id);
        $giver_account->role_id = 1;
        $giver_account->save();

        // отчет
        $report = new Report();
        $report->to_id = $giver->round->account_id;
        $report->from_id = $giver->account_id;
        $report->round_id = $giver->round_id;
        $report->amount = 0;
        $report->successful = false;
        $report->save();

        if($giver->is_priority && $giver->round_id != null) {
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

    public function start(Request $request)
    {
        $request->validate([
            'account_id' => ['required', 'integer'],
        ]);
        $account = UserAccount::find($request->get('account_id'));
        $user = Auth::user();

        if($account->roundType->is_need_pay == true) {
            if($user->count_avatars < 1) {
                return ResponseService::sendJsonResponse(
                    false,
                    403,
                    [],
                    []
                );
            }
            $user->count_avatars--;
            $user->save();
            TicketReportAction::create($user->id, 1, 'pay_account_start');
        }

        if ($account->role_id == 2 || !$request->get('account_id') || !$account) {
            return ResponseService::sendJsonResponse(
                false,
                303,
                [],
                []
            );
        }
        $account->role_id = 2;
        $account->save();

        if($account->next_round != 1) {
            $need_count_givers = RoundType::find($account->next_round)->count_givers;

            $round = Round::with(['roundGivers' => function ($query) {
                $query->where('status_id', '!=', 1);
                $query->where('status_id', '!=', 5);
                $query->where('status_id', '!=', 6);
                $query->where('status_id', '!=', 7);
            }])
                ->where('active', true)
                ->where('round_type_id', $account->next_round)
                ->where('account_id', '<', 5)
                ->first();

            if (!$round || (isset($round->roundGivers) && count($round->roundGivers) >= $need_count_givers)) {
                $rounds = Round::with(['roundGivers' => function ($query) {
                    $query->where('status_id', '!=', 1);
                    $query->where('status_id', '!=', 5);
                    $query->where('status_id', '!=', 6);
                    $query->where('status_id', '!=', 7);
                }])
                    ->where('active', true)
                    ->where('priority', true)
                    ->where('round_type_id', $account->next_round)
                    ->get();
            }

            if (!$round || (isset($round->roundGivers) && count($round->roundGivers) >= $need_count_givers) && !isset($rounds)) {
                $rounds = Round::with(['roundGivers' => function ($query) {
                    $query->where('status_id', '!=', 1);
                    $query->where('status_id', '!=', 5);
                    $query->where('status_id', '!=', 6);
                    $query->where('status_id', '!=', 7);
                }])
                    ->where('active', true)
                    ->where('round_type_id', $account->next_round)
                    ->get();
            }
            else {
                $rounds[] = $round;
            }

            $round_id = null;
            $receiver = null;
            foreach ($rounds as $round) {
                if (count($round->roundGivers) < $need_count_givers
                    && $account->user_id !== $round->account->user_id
                    && $round->account->user->updated_at >= Carbon::now()->subWeeks(1)->toDateTimeString()) {
                    $round_id = $round->id;
                    $receiver = $round->account_id;

                    try {
                        $tg_id = $round->account->user->telegram_id;
                        if ($tg_id != null) {
                            $text = "У вас появился даритель в целевой очереди, в аккаунте #".$round->account->number."
                            Для того, чтобы увидеть дарителя в кабинете необходимо активировать тикет на получение этого подарка.
                            Зайдите в личный кабинет, следуйте инструкции в очереди.
                            Обратите внимание на время, если не успеете активировать тикет на получение подарка, то придется ждать другого дарителя";
                            $keyboard = [
                                'text' => 'Перейти на сайт',
                                'url' => 'https://vbalance.net/auth',
                            ];
                            $this->sendTgMessage($tg_id, $text, $keyboard);
                        }
                    } catch (Exception $e) {}

                    $cel = true;

                    $round = $round;
                    break;
                }
            }
        }
        else {
            $round_id = null;
            $receiver = null;
            $cel = false;
        }
        if (!isset($cel))
            $cel = false;

        $giver = NewGiverAction::store($round_id, $account, $cel);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'account' => $account,
                'rounds' => $round ?? '',
                'receiver' => $receiver != null ? UserAccount::find($receiver)->user : '',
                'round_giver' => $giver && $giver->status_id == 1 ? '' : $giver,
            ]
        );
    }

    public function sendGift(Request $request) {
        $request->validate([
            'giver_id' => ['required', 'integer'],
        ]);

        $giver = RoundGiver::find($request->get('giver_id'));
        if($giver->status_id >= 3) {
            return ResponseService::sendJsonResponse(
                false,
                303,
                [],
                []
            );
        }
        $giver->status_id = 3;
        $giver->save();

        try {
            $tg_id = $giver->round->account->user->telegram_id;
            if($tg_id != null && Auth::user()->login != null) {
                $text = "Даритель " . Auth()->user()->login . " в аккаунте #" . $giver->account->number . " уже отправил вам подарок," .
                    "пожалуйста проверьте подарок и подтвердите " .
                    "получение на сайте. По любым спорным ситуациям обращайтесь в поддержку";
                $keyboard = [
                    'text' => 'Перейти на сайт',
                    'url' => 'https://vbalance.net/auth',
                ];
                $this->sendTgMessage($tg_id, $text, $keyboard);

                $text = "Даритель " . Auth()->user()->login . " отправил  подарок!";

                $this->sendTgMessage(-1001837591674, $text);

            }
        } catch (Exception $e) {}

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'round_giver' => $giver ?? '',
            ]
        );
    }

    public function confirm(Request $request) {
        $request->validate([
            'giver_id' => ['required', 'integer'],
            'account_id' => ['required', 'integer'],
        ]);

        $giver = RoundGiver::find($request->get('giver_id'));
        $us = UserAccount::find($request->get('account_id'));

        if($giver->status_id == 4 && Auth::user()->id != $us->user_id) {
            return ResponseService::sendJsonResponse(
                false,
                303,
                [],
                []
            );
        }
        // подтверждаем отправителю
        $giver->status_id = 4;
        $giver->save();

        // делаем его получателем
        $new_account = UserAccount::where('id', $giver->account_id)->first();

        if ($new_account->user_id == 8320) {
            $new_account->role_id = 1;
            $new_account->save();
        }
        else {
            $new_account->role_id = 3;

            $vesting = TokenVestingReport::where('user_id', $new_account->user_id)->where('type', 'gift_base')->count();
            if ($new_account->next_round == 1 && $vesting < 12) {
                $user_token = User::find($new_account->user_id);
                $user_token->token_vesting += 50 * 50;
                TokenVestingReportAction::create($user_token->id, 50 * 50, 'gift_base');
                $user_token->save();
            }

            $vesting = TokenVestingReport::where('user_id', $new_account->user_id)->where('type', 'gift_sto')->count();
            if ($new_account->next_round == 2 && $vesting < 3) {
                $user_token = User::find($new_account->user_id);
                $user_token->token_vesting += 100 * 50;
                TokenVestingReportAction::create($user_token->id, 100 * 50, 'gift_sto');
                $user_token->save();
            }

            $vesting = TokenVestingReport::where('user_id', $new_account->user_id)->where('type', 'gift_vacation')->count();
            if ($new_account->next_round == 3 && $vesting < 3) {
                $user_token = User::find($new_account->user_id);
                $user_token->token_vesting += 200 * 50;
                TokenVestingReportAction::create($user_token->id, 200 * 50, 'gift_vacation');
                $user_token->save();
            }

            $vesting = TokenVestingReport::where('user_id', $new_account->user_id)->where('type', 'gift_car')->count();
            if ($new_account->next_round == 4 && $vesting < 3) {
                $user_token = User::find($new_account->user_id);
                $user_token->token_vesting += 400 * 50;
                TokenVestingReportAction::create($user_token->id, 400 * 50, 'gift_car');
                $user_token->save();
            }

            $new_account->save();
        }

        // берем активный акк пользователю
        $account = UserAccount::with('role')->find($request->get('account_id'));

        // берем раунды получателя
        $round_active_user = Round::with(['roundGivers' => function($searsh) {
                $searsh->where('status_id', 4);
            }, 'roundGivers.account.user'])
            ->where('account_id', $account->id)
            ->orderBy('id', 'desc')
            ->first();

        // отчет
        $report = new Report();
        $report->to_id = $round_active_user->account_id;
        $report->from_id = $giver->account_id;
        $report->round_id = $giver->round_id;
        $report->amount = $round_active_user->type->price;
        $report->successful = true;
        $report->save();



        $this->sendTgMessage(
            '-1001837591674',
            $account->user->login." Получил подарок от".$us->user->login,
        );

        // создаем бывшему дарителю раунд
        if ($new_account->user_id != 8320) {
            $new_round = new Round();
            $new_round->account_id = $giver->account_id;
            $new_round->round_type_id = $round_active_user->round_type_id;
            $new_round->active = true;
            $new_round->verification_code = rand(1001, 9999);
            $new_round->price = $round_active_user->price;
            $new_round->save();

//            if($giver->account->roundType->is_need_pay == true) {
//                for ($i=0; $i < $giver->account->roundType->count_givers; $i++) {
//                    RoundGiverPay::create([
//                        'round_id' => $new_round->id,
//                    ]);
//                }
//            }
        }

        $need_count_givers = RoundType::find($account->next_round)->count_givers;

        // логика для получателя
        if($account->user->role_id == 4 && count($round_active_user->roundGivers) == $need_count_givers) {// если админ и если есть 3 завершенных дарителя
            if(RoundGiver::where('round_id', $round_active_user->id)
                    ->where(function ($query) {
                    $query->where('status_id', 2)
                        ->orWhere('status_id', 3)
                        ->orWhere('status_id', 8);
                })->count() == 0)
            {
                // закрываем раунд
                $round_active_user = $round_active_user;
                $round_active_user->active = false;
                $round_active_user->save();

                // убираем аккаунт из очереди
                $account->role_id = 1;
                $account->save();
            }
        }
        elseif(count($round_active_user->roundGivers) == $need_count_givers) { // если есть 3 завершенных дарителя
            if(RoundGiver::where('round_id', $round_active_user->id)
                    ->where(function ($query) {
                        $query->where('status_id', 2)
                            ->orWhere('status_id', 3)
                            ->orWhere('status_id', 8);
                })->count() == 0)
            {
                // закрываем раунд
                $round_active_user->active = false;
                $round_active_user->priority = false;
                $round_active_user->save();

                // убираем аккаунт из очереди
                $account->role_id = 1;

                $roundTypes = RoundType::where('queue', $round_active_user->type->queue)->get();

                foreach($roundTypes as $roundType) {
                    if($roundType->id > $round_active_user->round_type_id) {
                        $next_round_acc = $roundType->id;
                        break;
                    }
                }

                if(!isset($next_round_acc)) {
                    $account->next_round = 0;
                    $account->available = false;
                    $account->active = false;
                    $account->save();
                }
                else {
                    $account->next_round = $next_round_acc;
                    $account->save();
                }
            }
        }

        $account = UserAccount::where('user_id', Auth::user()->id)
            ->where('active', true)
            ->first();

        if($giver->round->round_type_id != 1 && $new_account->user_id != 8320) {
            // ищем, бывшему дарителю, дарителей
            $new_round_givers = RoundGiver::where('round_id', null)->get();
            $new_giver_count = 0;
            foreach($new_round_givers as $new_round_giver) {
                if($new_round_giver->account->user_id !== $new_round->account->user_id && $giver->round->round_type_id == $new_round_giver->account->next_round) {

                    $new_round_giver->round_id = $new_round->id;
                    if($new_round_giver->account->roundType->is_need_pay == true) {
                        $new_round_giver->status_id = 8;
                    }
                    else {
                        $new_round_giver->status_id = $new_round_giver->round_id != null ? 2 : null;
                    }
                    $new_round_giver->start = $new_round_giver->round_id != null ? Carbon::now() : null;
                    $new_round_giver->save();
                    $new_giver_count++;
                    if($new_giver_count == $need_count_givers)
                        break;
                }
            }
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
                    ->orderBy('id', 'desc')
                    ->first();
            }

            if($account->role_id == 2) {
                $round_giver = RoundGiver::where('account_id', $account->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if($round_giver->round_id != null) {
                    $receiver = $round_giver->round->account->user;
                    $round = $round_giver->round;
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
            ]
        );
    }
}
