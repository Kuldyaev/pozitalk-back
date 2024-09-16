<?php

namespace App\Services;

use App\Models\IndexDeposit;
use App\Models\IndexTokenInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class IndexToken
{
    public function structure(): array
    {

        $response = [];

        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();

        $indexTokenInfos = IndexTokenInfo::select('title', 'key', 'icon')->get();
        foreach ($indexTokenInfos as $indexTokenInfo) {
            if ($indexTokenInfo->key == 'bitcoin') {
                $indexTokenInfo->percent = intval($indexLast->bitcoin * 100);
            }
            if ($indexTokenInfo->key == 'ethereum') {
                $indexTokenInfo->percent = intval($indexLast->ethereum * 100);
            }
            if ($indexTokenInfo->key == 'arbitrum') {
                $indexTokenInfo->percent = intval($indexLast->arbitrum * 100);
            }
            if ($indexTokenInfo->key == 'optimism') {
                $indexTokenInfo->percent = intval($indexLast->optimism * 100);
            }
            if ($indexTokenInfo->key == 'polygon') {
                $indexTokenInfo->percent = intval($indexLast->polygon * 100);
            }
            if ($indexTokenInfo->key == 'polkadot') {
                $indexTokenInfo->percent = intval($indexLast->polkadot * 100);
            }
            if ($indexTokenInfo->key == 'ton') {
                $indexTokenInfo->percent = intval($indexLast->ton * 100);
            }
            if ($indexTokenInfo->key == 'solana') {
                $indexTokenInfo->percent = intval($indexLast->solana * 100);
            }
            if ($indexTokenInfo->key == 'apecoin') {
                $indexTokenInfo->percent = intval($indexLast->apecoin * 100);
            }
        }
        $response[] = $indexTokenInfos;

        return $response;
    }

    public function statistic(string $timeFilter): array
    {
        switch ($timeFilter) {
            case 'week':
                $startOfWeek = now()->subWeek();
                $endOfWeek = now();
                $statistics = \App\Models\IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->orderBy('id', 'asc')
                    ->get();
                break;
            case 'month':
                $startOfMonth = now()->subMonth();
                $endOfMonth = now();
                $statistics = \App\Models\IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('id', 'asc')
                    ->get();
                break;
            case 'all':
            default:
                $statistics = \App\Models\IndexToken::select('index_tokens.*')
                    ->join(\DB::raw('(SELECT MAX(id) as max_id, DATE_FORMAT(created_at, "%Y-%m-%d %H") as hour
                     FROM index_tokens
                     GROUP BY hour) as sub'), function ($join) {
                        $join->on('index_tokens.id', '=', 'sub.max_id');
                    })
                    ->orderBy('id', 'asc')
                    ->get();
                break;
        }

        $response = [];
        foreach ($statistics as $statistic) {
            $response[] = [
                'time' => Carbon::parse($statistic->created_at)->unix(),
                'value' => $statistic->index,
            ];
        }

        return $response;
    }

    public function history()
    {
        $reports = IndexDeposit::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            $result[] = [
                'data' => $report->created_at,
                'operation' => $report->is_active ? 'buy' : 'sale',
                'program' => $report->program_id,
                'vbti' => round($report->count_index, 2),
                'usdt' => round($report->amount, 2),
                'exchange_rate' => round($report->price_index, 2),
                'result' => $report->result,
                'average_price' => $report->average_price,
            ];
        }

        return $result;
    }

    public function indexInfo($code)
    {
        $depositsBuyAm = IndexDeposit::where('user_id', auth()->user()->id)
            ->where('program_id', $code)
            ->where('is_active', true)
            ->sum('amount');
        $depositsSellAm = IndexDeposit::where('user_id', auth()->user()->id)
            ->where('program_id', $code)
            ->where('is_active', false)
            ->sum('amount');
        $balance = $depositsBuyAm - $depositsSellAm;

        $depositsBuy = IndexDeposit::where('user_id', auth()->user()->id)
            ->where('program_id', $code)
            ->where('is_active', true)
            ->get();
        $depositsSell = IndexDeposit::where('user_id', auth()->user()->id)
            ->where('program_id', $code)
            ->where('is_active', false)
            ->get();

        $buyMed = $depositsBuy->isNotEmpty() ? round($depositsBuy->avg('amount'), 2) : 0;
        $sellMed = $depositsSell->isNotEmpty() ? round($depositsSell->avg('amount'), 2) : 0;

        Artisan::call('index:cron');
        $indexLast = \App\Models\IndexToken::orderBy('id', 'desc')->first();

        $result = [
            'balance' => [
                'usdt' => round($balance, 2),
                'vbti' => $balance * $indexLast->index,
            ],
            'statistic' => [
                'buy' => [
                    'vbti' => round(
                        IndexDeposit::where('user_id', auth()->user()->id)
                            ->where('program_id', $code)
                            ->where('is_active', true)
                            ->sum('count_index'),
                        2
                    ),
                    'usdt' => round(
                        IndexDeposit::where('user_id', auth()->user()->id)
                            ->where('program_id', $code)
                            ->where('is_active', true)
                            ->sum('amount'),
                        2
                    ),
                    'average' => $buyMed,
                ],
                'sell' => [
                    'vbti' => round(
                        IndexDeposit::where('user_id', auth()->user()->id)
                            ->where('program_id', $code)
                            ->where('is_active', false)
                            ->sum('count_index'),
                        2
                    ),
                    'usdt' => round(
                        IndexDeposit::where('user_id', auth()->user()->id)
                            ->where('program_id', $code)
                            ->where('is_active', false)
                            ->sum('amount'),
                        2
                    ),
                    'average' => $sellMed,
                ],
            ]
        ];

        return $result;
    }
}
