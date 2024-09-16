<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\V1\Controller;
use App\Models\Round;
use App\Models\RoundGiver;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class RoundController extends Controller
{

    public function showReports(Request $request) {
        $request->validate([
            'sortBy' => 'string',
            'sort' => 'string',
            'product' => 'string',
            'searchBy' => 'string',
            'query' => 'string',
        ]);

        $products_bd = UsdtTransaction::select('product')->distinct()->get();
        $products = [];
        foreach ($products_bd as $product) {
            if($product->product == 'academy') {
                $products[] = [
                    'label' => 'Академия 1',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'academy_2') {
                $products[] = [
                    'label' => 'Академия 2',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'academy_3') {
                $products[] = [
                    'label' => 'Академия 3',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'academy_4') {
                $products[] = [
                    'label' => 'Академия 4',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'bronze') {
                $products[] = [
                    'label' => 'Статус советник',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'silver') {
                $products[] = [
                    'label' => 'Статус ментор',
                    'value' => $product->product
                ];

            }
            elseif($product->product == 'gold') {
                $products[] = [
                    'label' => 'Статус мастер',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'platinum') {
                $products[] = [
                    'label' => 'Статус партнер',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'account') {
                $products[] = [
                    'label' => 'Тикеты',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_1') {
                $products[] = [
                    'label' => 'Пакет life 1',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_2') {
                $products[] = [
                    'label' => 'Пакет life 2',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_3') {
                $products[] = [
                    'label' => 'Пакет life 3',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_4') {
                $products[] = [
                    'label' => 'Пакет life 4',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_5') {
                $products[] = [
                    'label' => 'Пакет life 5',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'life_6') {
                $products[] = [
                    'label' => 'Пакет life 6',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'token_private') {
                $products[] = [
                    'label' => 'Доля',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'arb_deposit') {
                $products[] = [
                    'label' => 'Депозит ARB',
                    'value' => $product->product
                ];
            }
            elseif($product->product == 'balance_plus') {
                $products[] = [
                    'label' => 'Пополнение баланса',
                    'value' => $product->product
                ];
            }
            elseif($product->product != '' && $product->product != null) {
                $products[] = [
                    'label' => $product->product,
                    'value' => $product->product
                ];
            }
        }

        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        $query = UsdtTransaction::orderBy($sort_by, $sort);

        $fail = false;
        if ($request->filled('searchBy') && $request->filled('query')) {
            if($request->get('searchBy') == 'user_id') {
                $searchBy = 'id';
            }
            elseif($request->get('searchBy') == 'telegram_name') {
                $searchBy = 'telegram_name';
            }
            elseif($request->get('searchBy') == 'login') {
                $searchBy = 'login';
            }

            $users = User::where($searchBy, 'like', '%'. $request->get('query') .'%')->get();
            foreach($users as $user)
            {
                $ids[]=$user->id;
            }

            if($users) {
                $query = UsdtTransaction::WhereIn('user_id', $ids)->orderBy($sort_by, $sort);
            }
            else {
                $fail = true;
            }
        }

        if ($request->get('product') == 'account') {
            $query->where('product', 'like', '%account%');
        }
        elseif ($request->filled('product')) {
            $query->where('product', $request->get('product'));
        }

        $reports = $query->paginate(15);

        $result = [];
        if($fail == false) {
            foreach ($reports as $report) {
                foreach ($products as $product) {
                    if($report->product == $product['value']) {
                        $product_name = $product['label'];
                    }
                }

                $result[] = [
                    'id' => $report->id,
                    'id_user' => $report->user->id,
                    'login' => $report->user->login ?? null,
                    'telegram_name' => $report->user->telegram_name ?? null,
                    'product' => $product_name ?? null,
                    'sum_usd' => $report->sum_usd ?? null,
                    'is_admin' => $report->transaction_id == 11111 ?? true,
                    'created_at' => $report->created_at,
                    'address' => $report->address ?? null,
                ];
            }
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'report' => $result,
                'products' => $products,
                'total' => $reports->total(),
            ]
        );
    }

    public function showFreeGivers() {
        $count_free_givers = RoundGiver::where('round_id', null)
            ->where('status_id', 1)
            ->where('round_type_id', 1)
            ->count();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'count_free_givers' => $count_free_givers,
            ]
        );
    }

    public function congratulated($id) {
        $giver = RoundGiver::find($id);
        $giver->is_congratulated = $giver->is_congratulated == true ? false : true;
        $giver->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'giver' => $giver,
            ]
        );
    }

    public function showGivers(Request $request) {
        $request->validate([
            'sortBy' => 'string',
            'sort' => 'string',
            'type_givers' => 'integer',
        ]);

        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        $query = RoundGiver::orderBy($sort_by, $sort);

        if ($request->get('type_givers') == 2) {
            $query->where('status_id', 4);
        }
        elseif ($request->get('type_givers') == 3) {
            $query->where('is_distributed', true);
        }

        $givers = $query->paginate(15);

        $result = [];
        foreach ($givers as $giver) {
            $result[] = [
                'id' => $giver->id,
                'id_from' => $giver->account->user->id,
                'login_from' => $giver->account->user->login,
                'id_to' => $giver->round->account->user->id ?? null,
                'login_to' => $giver->round->account->user->login ?? null,
                'round_type_id' => $giver->account->next_round,
                'status_id' => $giver->status_id,
                'is_distributed' => $giver->is_distributed,
                'is_congratulated' => $giver->is_congratulated,
                'start' => $giver->start,
                'created_at' => $giver->created_at,
                'updated_at' => $giver->updated_at,
            ];
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'givers' => $result,
                'total' => $givers->total(),
            ]
        );
    }

    public function distributionFreeGivers(Request $request) {
        $request->validate([
            'count_free_givers' => 'required|integer',
            'type_distribution' => 'required|integer',
        ]);
        $type_distribution = $request->get('type_distribution');

        $count_free_givers = $request->get('count_free_givers');
        $free_givers = RoundGiver::where('round_id', null)
            ->where('status_id', 1)
            ->where('round_type_id', 1)
            ->take($count_free_givers)
            ->get();

        if($type_distribution == 1) {
            $rounds = Round::with(['roundGivers' => function ($query) {
                    $query->where('status_id', 2)
                        ->orWhere('status_id', 3)
                        ->orWhere('status_id', 4)
                        ->orWhere('status_id', 8);
                }])
                ->where('active', true)
                ->where(function ($q) {
                    $q->where('account_id', '<', 5)
                        ->orWhere('priority', true);
                })
                ->where('round_type_id', 1)
                ->get();

            foreach ($free_givers as $free_giver) {
                foreach ($rounds as $round) {
                    if (count($round->roundGivers) < 1
                        && $free_giver->account->user_id !== $round->account->user_id)
                    {
                        $free_giver->round_id = $round->id;
                        $free_giver->status_id = 8;
                        $free_giver->start = $free_giver->round_id != null ? Carbon::now() : null;
                        $free_giver->is_distributed = true;
                        $free_giver->is_priority = true;
                        $free_giver->save();
                        $round->roundGivers->add($free_giver);
                        $count_free_givers--;

                        try {
                            $tg_id = $round->account->user->telegram_id;
                            if ($tg_id != null) {
                                $text = "У вас появился даритель в базовой очереди, в аккаунте #".$round->account->number."
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
                        break;
                    }

                    if (count($round->roundGivers) > 0) {
                        $round->priority = false;
                        $round->save();
                    }
                }
            }
        }
        elseif($type_distribution == 2) {
            $free_givers = RoundGiver::where('round_id', null)
                ->where('status_id', 1)
                ->where('round_type_id', 1)
                ->take($count_free_givers)
                ->get();

            $count_free_givers = intdiv(count($free_givers), 3);
            $free_giver_groups = $free_givers->chunk($count_free_givers);

            $rounds = Round::with(['roundGivers' => function ($query) {
                $query->where('status_id', 2)
                    ->orWhere('status_id', 3)
                    ->orWhere('status_id', 4)
                    ->orWhere('status_id', 8);
            }])
                ->where('account_id', '>=', 5)
                ->where('priority', false)
                ->where('active', true)
                ->where('round_type_id', 1)
                ->where('created_at', '<=', '2023-01-20 00:00:00')
                ->get();
            $count_rounds = intdiv(count($rounds), 3);
            $rounds = $rounds->chunk($count_rounds);

            $i = 0;
            foreach ($free_giver_groups as $kg => $free_giver_group) {
                foreach ($free_giver_group as $k => $free_giver) {
                    if(isset($rounds[$i])) {
                        foreach ($rounds[$i] as $round) {
                            if (count($round->roundGivers) < 1
                                && $free_giver->account->user_id !== $round->account->user_id
                                && $round->account->user->updated_at >= Carbon::now()->subWeeks(1)->toDateTimeString())
                            {
                                $free_giver->round_id = $round->id;
                                $free_giver->status_id = 8;
                                $free_giver->start = $free_giver->round_id != null ? Carbon::now() : null;
                                $free_giver->is_distributed = true;
                                $free_giver->save();
                                $round->roundGivers->add($free_giver);

                                try {
                                    $tg_id = $round->account->user->telegram_id;
                                    if ($tg_id != null) {
                                        $text = "У вас появился даритель в базовой очереди, в аккаунте #".$round->account->number."
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
                                break;
                            }
                        }
                    }
                }

                $i++;
                if($i > 2) {
                    break;
                }
            }
        }

        $count_free_givers = RoundGiver::where('round_id', null)
            ->where('status_id', 1)
            ->where('round_type_id', 1)
            ->count();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'count_free_givers' => $count_free_givers,
            ]
        );
    }
}
