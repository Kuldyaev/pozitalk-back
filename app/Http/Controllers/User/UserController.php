<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\ReportReferral;
use App\Models\Seling;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class UserController extends Controller
{
    #[Get(
        path: '/user/me',
        operationId: 'getMe',
        tags: ['User'],
        security: [['bearerAuth' => []]],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(
                    ref: '#/components/schemas/User',
                    type: 'object',
                )
            )
        ]
    )]
    public function me(Request $request): UserResource
    {
        $user = auth()->user();
        $user->status = $user->getStatus();
        $user->pools = $user->status != 'base' ? $user->getUserPools($user->status) : [];

        return new UserResource($user);
    }

    #[Get(
        path: '/user/personal-link',
        description: "Персональная ссылка.",
        tags: ['User'],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function personalLink(Request $request): JsonResponse
    {
        $user = auth()->user();
        $subWeek = Carbon::now()->subWeek();
        $subMonth = Carbon::now()->subMonth();

        $cachedData = Cache::get('user_referrals_short_info_' . $user->id);
        if ($cachedData) {
            return new JsonResponse([
                'data' => $cachedData
            ]);
        }

        $childrens = User::where('referal_id', $user->id)->get();
        $total = 0;
        $straight_total = 0;
        $five_lines_total = 0;
        $user_week = 0;
        $user_month = 0;
        if ($childrens) {
            $ids = [];
            $week_ids = [];
            $month_ids = [];
            foreach ($childrens as $u) {
                $ids[] = $u->id;

                if ($u->created_at >= $subWeek) {
                    $week_ids[] = $u->id;
                }
                if ($u->created_at >= $subMonth) {
                    $month_ids[] = $u->id;
                }
            }
            $total = count($ids);
            $straight_total += count($ids);
            $five_lines_total += count($ids);
            $user_week += count($week_ids);
            $user_month += count($month_ids);

            $i = 1;
            while (true) {
                if ($ids) {
                    $users = User::whereIn('referal_id', $ids)->get();
                    if ($users) {
                        $ids = [];
                        $week_ids = [];
                        $month_ids = [];
                        foreach ($users as $u) {
                            $ids[] = $u->id;

                            if ($u->created_at >= $subWeek) {
                                $week_ids[] = $u->id;
                            }
                            if ($u->created_at >= $subMonth) {
                                $month_ids[] = $u->id;
                            }
                        }

                        $user_week += count($week_ids);
                        $user_month += count($month_ids);

                        $total += count($ids);
                        if ($i <= 4) {
                            $five_lines_total += count($ids);
                        }
                        $i++;
                    } else
                        break;
                } else
                    break;
            }
        }

        $response = [
            'referral_code' => $user->referal_invited,
            'referrals' => [
                'total' => $total,
                'week' => $user_week,
                'month' => $user_month,
            ]
        ];

        Cache::put('user_referrals_short_info_' . $user->id, $response, Carbon::now()->addMinutes(30));

        return new JsonResponse([
            'data' => $response
        ]);
    }


    #[Get(
        path: '/user/profile/statuses-and-founders',
        description: "Карьерный план и статусы",
        tags: ['User'],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
            )
        ]
    )]
    public function statusesAndPools(): JsonResponse
    {
        $userId = auth()->user()->id;

        // Определение уровней founder и порогов товарооборота для каждого уровня
        $founderLevels = [
            'founder1' => 100000,
            'founder2' => 250000,
            'founder3' => 500000,
            'founder4' => 1000000,
        ];

        // Получаем товарооборот самого пользователя
        $userTurnover = Seling::where('member_id', $userId)
            ->sum('sum');

        // Формируем ответ с информацией по уровням
        $response = [
            'statuses' => [
                'platinum' => [
                    'sum_months' => round(ReportReferral::where('type', 'pool')
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->sum('sum')),
                ],
                'gold' => [
                    'sum_months' => round(ReportReferral::where('type', 'pool-gold')
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->sum('sum')),
                ],
                'silver' => [
                    'sum_months' => round(ReportReferral::where('type', 'pool-silver')
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->sum('sum')),
                ],
                'bronze' => [
                    'sum_months' => round(ReportReferral::where('type', 'pool-bronze')
                        ->where('created_at', '>=', Carbon::now()->subDays(30))
                        ->sum('sum')),
                ],
            ],
            'founders' => []
        ];

        // Проходим по каждому уровню founder и вычисляем товарооборот для него
        foreach ($founderLevels as $levelName => $threshold) {
            // Получаем товарооборот рефералов с учетом ограничения на 50% для текущего уровня
            $referalTurnover = DB::table('users as u')
                ->join('selings as s', 'u.id', '=', 's.member_id')
                ->where('u.referal_id', $userId)
                ->select(DB::raw("u.id, SUM(s.sum) as total_sum, LEAST(SUM(s.sum), {$threshold} * 0.5) as capped_sum"))
                ->groupBy('u.id')
                ->get();

            // Общий товарооборот пользователя с учетом рефералов для текущего уровня
            $totalTurnover = $userTurnover + $referalTurnover->sum('capped_sum');

            $response['founders'][$levelName] = [
                'sum_months' => round(ReportReferral::where('type', $levelName)
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->sum('sum')),
                'selling' => $totalTurnover > $threshold ? $threshold : $totalTurnover,
            ];
        }

        return new JsonResponse([
            'data' => $response
        ]);
    }
}
