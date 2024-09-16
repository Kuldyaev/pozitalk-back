<?php

namespace App\Http\Controllers\V1;

use App\Models\ReportReferral;
use App\Models\Seling;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use function Symfony\Component\Routing\Loader\load;

class HierarchyController extends Controller
{
    public function userHierarchy(Request $request) {
        $request->validate([
            'user_id' => 'required|integer',
        ]);

        $childrens = User::where('referal_id', $request->get('user_id'))->get();
        if ($childrens) {
            foreach($childrens as $k=>$children) {
                $total = 0;
                $straight_total = 0;
                if($childrens) {
                    $ids = [];
                    foreach($childrens as $u) {
                        $ids[] = $u->id;
                    }
                    $straight_total += count($ids);

                    while (true) {
                        if($ids) {
                            $users = User::whereIn('referal_id', $ids)->get();
                            if ($users) {
                                $ids = [];
                                foreach ($users as $u) {
                                    $ids[] = $u->id;
                                }
                                $total += count($ids);
                            }
                            else
                                break;
                        }
                        else
                            break;
                    }
                }

                $childrens[$k]['totals'] = [
                    'straight' => $straight_total,
                    'total' => $total,
                ];

                $childrens[$k]['accounts'] = UserAccount::where('user_id', $children->id)->where('available', true)->get();
            }
        }

        return response([
            'children' => $childrens ?? null
        ]);
    }

    public function index(Request $request) {
        $request->validate([
            'user_login' => 'string',
        ]);

        $user = Auth::user()->append('totals');

        if($request->filled('user_login')) {
            $login = $request->get('user_login');

            $childrens = $user->children()
                ->where('login', 'like', '%'.$login.'%')
                ->paginate(5)
                ->append('totals')
                ->each(function (User $user) {
                    return $user['children'] = $user->children
                        ->append('totals');
                });
        }
        else {
            $childrens = $user->children()
                ->paginate(5)
                ->append('totals')
                ->each(function (User $user) {
                    return $user['children'] = $user->children
                        ->append('totals');
                });
        }

        foreach($childrens as $k=>$children) {
            $childrens[$k]['accounts'] = UserAccount::where('user_id', $children->id)->where('available', true)->get();


            $childrens_new = array();
            if(isset($children['children'][0])) {
                $childrens_new[0] = $children['children'][0];
                $childrens_new[0]['accounts'] = UserAccount::where('user_id', $children['children'][0]['id'])->where('available', true)->get();
            }
            if(isset($children['children'][1])) {
                $childrens_new[1] = $children['children'][1];
                $childrens_new[1]['accounts'] = UserAccount::where('user_id', $children['children'][1]['id'])->where('available', true)->get();
            }
            if(isset($children['children'][2])) {
                $childrens_new[2] = $children['children'][2];
                $childrens_new[2]['accounts'] = UserAccount::where('user_id', $children['children'][2]['id'])->where('available', true)->get();
            }
            $children['childrens'] = $childrens_new;
        }

//        $is_report = $user->reportReferrals()->exists();
//        $sum = $is_report ? $user->reportReferrals()->sum('sum') : 0;

        $count_avatar = 0;
        $count_avatar_two = 0;
        $count_avatar_three = 0;
        $count_avatar2 = null;

        $count_avatar = $user->accounts()
            ->where('next_round', 1)
            ->whereDate('created_at', '>=', Carbon::parse('10.01.2023'))
            ->count();
        $count_avatar = $count_avatar > 0 ? $count_avatar - 1 : $count_avatar;

        $count_avatar_two = $user->accounts()
            ->where('next_round', 2)
            ->whereDate('created_at', '>=', Carbon::parse('10.01.2023'))
            ->count();
        $count_avatar_two = $count_avatar_two > 0 ? $count_avatar_two - 1 : $count_avatar_two;

        $count_avatar_three = $user->accounts()
            ->where('next_round', 3)
            ->whereDate('created_at', '>=', Carbon::parse('10.01.2023'))
            ->count();
        $count_avatar_three = $count_avatar_three > 0 ? $count_avatar_three - 1 : $count_avatar_three;

//        $parent_ref = $user->parentRef->where('is_action', true);


        //TODO: для работы нужен nestedSet
        $all_users = 0;
        $all_users_one = 0;
        $all_users_two = 0;
        $all_users_three = 0;

        $count_avatar = 0;
        $count_avatar_two = 0;
        $count_avatar_three = 0;

        $array_lines = [
            $all_users_one_line_one = 0,
            $all_users_one_line_two = 0,
            $all_users_one_line_three = 0,
            $all_users_one_line_four = 0,
            $all_users_one_line_five = 0,
            $all_users_one_line_six = 0,
            $all_users_one_line_seven = 0,
            $all_users_one_line_eight = 0,
            $all_users_one_line_nine = 0,
            $all_users_one_line_ten = 0,
            $all_users_one_line_eleven = 0,
            $all_users_one_line_twelve = 0,
            $all_users_one_line_thirteen = 0,
            $all_users_one_line_fourteen = 0,
            $all_users_one_line_fifteen = 0,
        ];

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

            $lines[1] = count($ids);

            $dop_avatars = $own_users->sum('count_avatars');

            $acs1 = UserAccount::WhereIn('user_id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->count();
            $count_avatar += $dop_avatars + $acs1 - User::WhereIn('id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->where('created_at', '<', '2023-01-26 01:00:00')->count();
            $i = 1;
            $promo_tickets = 0;
            $promo_packages = 0;
            while (true)
            {
                if($ids)
                {
                    $users = User::WhereIn('referal_id', $ids)->get();
                    $dop_avatars = $users->sum('count_avatars');

                    if ($users) {
                        $i++;
                        $ids = array();
                        foreach ($users as $u) {
                            $ids[] = $u->id;
                        }

                        if($i <= 5) {
                            $promo_tickets += UsdtTransaction::whereIn('user_id', $ids)->where('product', 'account')->where('created_at', '>', '2023-03-25 00:00:00')->sum('sum_usd')/10;
                            $promo_packages += UsdtTransaction::whereIn('user_id', $ids)
                                ->where(function($query) {
                                    $query->where('product', 'bronze')
                                        ->orWhere('product', 'silver')
                                        ->orWhere('product', 'gold')
                                        ->orWhere('product', 'platinum');
                                })
                                ->where('created_at', '>', '2023-03-25 00:00:00')
                                ->sum('sum_usd');
                        }

                        $total += count($ids);
                        $lines[] = count($ids);

                        if($i <= 5)
                            $acs1 = UserAccount::WhereIn('user_id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->count();
                        elseif($i <= 8 || $i >= 6)
                            $acs2 = UserAccount::WhereIn('user_id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->count();
                        elseif($i > 8)
                            $acs3 = UserAccount::WhereIn('user_id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->count();

                        if($i <= 5)
                            $count_avatar += $dop_avatars + $acs1 - User::WhereIn('id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->where('created_at', '<', '2023-01-26 01:00:00')->count();
                        elseif($i <= 8 || $i >= 6)
                            $count_avatar_two += $dop_avatars + $acs2 - User::WhereIn('id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->where('created_at', '<', '2023-01-26 01:00:00')->count();
                        elseif($i > 8)
                            $count_avatar_three += $dop_avatars + $acs3 - User::WhereIn('id', $ids)->where('created_at', '>', '2023-01-10 00:00:00')->where('created_at', '<', '2023-01-26 01:00:00')->count();
                    }
                    else
                        break;
                }
                else
                    break;
            }
        }


        $count_avatar = ReportReferral::where('member_id', Auth::user()->id)->where('type', 'ticket')->where('line', '<=', 5)->sum('sum') / 0.3;
//
        $count_avatar_two = ReportReferral::where('member_id', Auth::user()->id)->where('line', '>=', 6)->where('line', '<=', 8)->sum('sum') / 0.1;
//
        $count_avatar_three = ReportReferral::where('member_id', Auth::user()->id)->where('line', '>=', 9)->where('line', '<=', 15)->sum('sum') / 0.1;

        $all_users = $total;

        foreach ($lines as $key => $line) {
            if($key <= 5) {
                $all_users_one += $line;
            }
            elseif($key <= 8 && $key >= 6) {
                $all_users_two += $line;
            }
            elseif($key > 8) {
                $all_users_three += $line;
            }

            foreach($array_lines as $k => $array_line) {
                if($k+1 == $key) {
                    $array_lines[$k] = $line;
                }
            }
        }

        $user = Auth::user();

        $dynamic_status_one = '0';
        if ($user->commission == 0.3 ) {
            $dynamic_status_one = '0.30';
        }

        if ($user->commission == 0.5 ) {
            $dynamic_status_one = '0.50';
        }

        if ($user->commission == 0.7 ) {
            $dynamic_status_one = '0.70';
        }

        if ($user->commission == 1.0 ) {
            $dynamic_status_one = '1';
        }

        $status = UsdtTransaction::where('user_id', Auth::user()->id)
            ->where(function($query) {
                $query->where('product', 'bronze')
                    ->orWhere('product', 'silver')
                    ->orWhere('product', 'gold')
                    ->orWhere('product', 'platinum');
            })
            ->orderBy('id', 'desc')
            ->first();

        $bronze = ReportReferral::where('member_id', Auth::user()->id)->where('type', 'bronze')->get();
        $silver = ReportReferral::where('member_id', Auth::user()->id)->where('type', 'silver')->get();
        $gold = ReportReferral::where('member_id', Auth::user()->id)->where('type', 'gold')->get();
        $platinum = ReportReferral::where('member_id', Auth::user()->id)->where('type', 'platinum')->get();
        $mark = [
            'allUsers' => $all_users,
            'statuses' => [
                [
                    'status' => 'basic',
                    'active' => $user->commission == 0.3,
                    'percent' => '0.30'
                ],
                [
                    'status' => 'bronze',
                    'active' => isset($status->product) && $status->product == 'bronze',
                    'percent' => '0.50'
                ],
                [
                    'status' => 'silver',
                    'active' => isset($status->product) && $status->product == 'silver',
                    'percent' => '0.70'
                ],
                [
                    'status' => 'gold',
                    'active' => isset($status->product) && $status->product == 'gold',
                    'percent' => '1'
                ],
                [
                    'status' => 'platinum',
                    'active' => isset($status->product) && $status->product == 'platinum',
                    'percent' => '1+'
                ]
            ],
            'lines' => [
                [
                    'avalible' => $user->commission == 0.3 || $user->commission == 0.5 || $user->commission == 0.7 || $user->commission == 1.0,
                    'allUsers' => (int) $all_users_one,
                    'users' => [
                        [
                            'number' => 1,
                            'count' => $array_lines[0],
                            'price' => $user->commission
                        ],
                        [
                            'number' => 2,
                            'count' => $array_lines[1],
                            'price' => $user->commission
                        ],
                        [
                            'number' => 3,
                            'count' => $array_lines[2],
                            'price' => $user->commission
                        ],
                        [
                            'number' => 4,
                            'count' => $array_lines[3],
                            'price' => $user->commission
                        ],
                        [
                            'number' => 5,
                            'count' => $array_lines[4],
                            'price' => $user->commission
                        ],
                    ],
                    'statistic' => [
                        'allAvatars' => (int) $count_avatar,
                        //'got' => $sum,
                        'got' => round(ReportReferral::where('member_id', $user->id)->where('type', 'ticket')->where('line', '<=', 5)->sum('sum'), 2),
                        'dynamicStatus' => $user->commission,
                        'shouldGet' => [
                            [
                                'count' => round($count_avatar * 0.5, 2),
                                'price' => $user->commission === 0.5
                                    ? null
                                    : (($user->commission === 0.7 || $user->commission === 1.0)
                                        ? null
                                        : '0.50')
                            ],
                            [
                                'count' => round($count_avatar * 0.7, 2),
                                'price' => $user->commission === 0.7
                                    ? null
                                    : ($user->commission === 1.0
                                        ? null
                                        : '0.70')
                            ],
                            [
                                'count' => round($count_avatar, 2),
                                'price' => $user->commission === 1.0 ? null : '1'
                            ],
                        ]
                    ],
                    'packages' => [
                        'bronze_count' => count($bronze),
                        'bronze_sum' => $bronze->sum('sum'),
                        'silver_count' => count($silver),
                        'silver_sum' => $silver->sum('sum'),
                        'gold_count' => count($gold),
                        'gold_sum' => $gold->sum('sum'),
                        'platinum_count' => count($platinum),
                        'platinum_sum' => $platinum->sum('sum'),
                    ]
                ],
//                [
//                    'avalible' => $user->commission === 0.7 || $user->commission === 1.0,
//                    'allUsers' => $all_users_two,
//                    'users' => [
//                        [
//                            'number' => 6,
//                            'count' => $array_lines[5],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 7,
//                            'count' => $array_lines[6],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 8,
//                            'count' => $array_lines[7],
//                            'price' => '0.10'
//                        ],
//                    ],
//                    'statistic' => [
//                        'allAvatars' => $count_avatar_two,
//                        //'got' => $sum,
//                        'got' => ReportReferral::where('member_id', Auth::user()->id)->where('line', '>=', 6)->where('line', '<=', 8)->sum('sum'),
//                        'shouldGet' => [
////                            [
////                                'count' => round($count_avatar_two * 0.5, 2),
////                                'price' => '0.10'
////                            ],
////                            [
////                                'count' => round($count_avatar_two * 0.7, 2),
////                                'price' => '0.10'
////                            ],
//                            [
//                                'count' => round($count_avatar_two * 0.1, 2),
//                                'price' => '0.10'
//                            ],
//                        ]
//                    ]
//                ],
//                [
//                    'avalible' => $user->commission === 1.0,
//                    'allUsers' => $all_users_three,
//                    'users' => [
//                        [
//                            'number' => 9,
//                            'count' => $array_lines[8],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 10,
//                            'count' => $array_lines[9],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 11,
//                            'count' => $array_lines[10],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 12,
//                            'count' => $array_lines[11],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 13,
//                            'count' => $array_lines[12],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 14,
//                            'count' => $array_lines[13],
//                            'price' => '0.10'
//                        ],
//                        [
//                            'number' => 15,
//                            'count' => $array_lines[14],
//                            'price' => '0.10'
//                        ],
//                    ],
//                    'statistic' => [
//                        'allAvatars' => $count_avatar_three,
//                        //'got' => $sum,
//                        'got' => ReportReferral::where('member_id', Auth::user()->id)->where('line', '>=', 9)->where('line', '<=', 15)->sum('sum'),
//                        'shouldGet' => [
////                            [
////                                'count' => round($count_avatar_three * 0.5, 2),
////                                'price' => '0.10'
////                            ],
////                            [
////                                'count' => round($count_avatar_three * 0.7, 2),
////                                'price' => '0.10'
////                            ],
//                            [
//                                'count' => round($count_avatar_three * 0.1, 2),
//                                'price' => '0.10'
//                            ],
//                        ]
//                    ]
//                ]
            ],
        ];

        $promo = [
            'is_active' => true,
            'info' => [
                'tickets' => (int) $promo_tickets,
                'packages' => (int) $promo_packages,
            ]
        ];

        $hystorys = ReportReferral::where('member_id', Auth::user()->id)->where('sum', '!=', 0)->orderBy('created_at', 'desc')->limit(200)->get();
        foreach ($hystorys as $hystory) {
            $hystory['user_from'] = User::where('id', $hystory->owner_id)->first() ?? null;
        }

        $respons = [
            'children' => $childrens ?? null,
            'user' => $user,
//            '$count_avatar2' => $count_avatar2 ?? null,
            'mark' => $mark,
            'lines' => $lines,
            'promo' => $promo,
            'hystory' => $hystorys,
            'turnover' => [
                'personal' => Seling::where('member_id', Auth::user()->id)->where('line', 0)->sum('sum'),
                'in_hierarchy' => Seling::where('member_id', Auth::user()->id)->where('line', '>', 0)->sum('sum'),
            ]
        ];

        return response($respons);
//        return $a;
    }

    private function recursive($referral)
    {
        $result = 0;

        if ($referral === null) {
            return $result;
        }

        $result = ++$result;

        $line = $referral->line;

        foreach (($referral?->referral?->parentRef->where('line', $line) ?? []) as $item) {
            $result = ++$result;
        }

        return $result;
    }
}
