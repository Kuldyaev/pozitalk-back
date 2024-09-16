<?php

namespace App\Http\Controllers\V1;

use App\Models\ReportReferral;
use App\Models\TicketReport;
use App\Models\TokenPrivateReport;
use App\Models\TokenRate;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class VbtPrivateController extends Controller
{
    public function index() {
        $user = Auth::user();
        $rate = TokenRate::first();

        $line1_ids = [];
        $line2_ids = [];
        $line3_ids = [];
        $line4_ids = [];
        $line5_ids = [];

        $users1 = User::where('referal_id', $user->id)->get();
        foreach ($users1 as $user1) {
            $line1_ids[] = $user1->id;
        }
        $line1_count_shares = intdiv(UsdtTransaction::whereIn('user_id', $line1_ids)->where('product', 'token_private')->sum('sum_usd'), 995);

        $users2 = User::whereIn('referal_id', $line1_ids)->get();
        foreach ($users2 as $user2) {
            $line2_ids[] = $user2->id;
        }
        $line2_count_shares = intdiv(UsdtTransaction::whereIn('user_id', $line2_ids)->where('product', 'token_private')->sum('sum_usd'), 995);

        $users3 = User::whereIn('referal_id', $line2_ids)->get();
        foreach ($users3 as $user3) {
            $line3_ids[] = $user3->id;
        }
        $line3_count_shares = intdiv(UsdtTransaction::whereIn('user_id', $line3_ids)->where('product', 'token_private')->sum('sum_usd'), 995);

        $users4 = User::whereIn('referal_id', $line3_ids)->get();
        foreach ($users4 as $user4) {
            $line4_ids[] = $user4->id;
        }
        $line4_count_shares = intdiv(UsdtTransaction::whereIn('user_id', $line4_ids)->where('product', 'token_private')->sum('sum_usd'), 995);

        $users5 = User::whereIn('referal_id', $line4_ids)->get();
        foreach ($users5 as $user5) {
            $line5_ids[] = $user5->id;
        }
        $line5_count_shares = intdiv(UsdtTransaction::whereIn('user_id', $line5_ids)->where('product', 'token_private')->sum('sum_usd'), 995);

        $private_can_pay = TicketReport::where('user_id', $user->id)->where('type', 'private')->where('created_at', '>=', '2023-11-14 00:00:00')->sum('count') / 10 ?? 0;
        $private_pay = UsdtTransaction::where('user_id', $user->id)->where('product', 'token_private')->where('created_at', '>=', '2023-11-14 00:00:00')->sum('sum_usd') / 1000 ?? 0;
        if($private_pay != 0) {
            $can_pay = $private_can_pay - $private_pay;
        }
        else {
            $can_pay = $private_can_pay;
        }

        $response = [
            'banner' => [
                'buy_count_all' => TokenPrivateReport::sum('count') / (1000/$rate->private_rate),
                'buy_count_user' => TokenPrivateReport::where('user_id', $user->id)->sum('count') / (1000/$rate->private_rate),
                'count_private_tokens' => $user->token_private,
                'usd_from_private_tokens' => $user->token_private / (1/$rate->private_rate),
                'private_tokens_rate' => $rate->private_rate,
                'can_pay' => $can_pay ?? 0,
            ],
            'active_user_shares' => [
                'line1' => [
                    'count_users' => count($line1_ids),
                    'count_shares' => $line1_count_shares,
                ],
                'line2' => [
                    'count_users' => count($line2_ids),
                    'count_shares' => $line2_count_shares,
                ],
                'line3' => [
                    'count_users' => count($line3_ids),
                    'count_shares' => $line3_count_shares,
                ],
                'line4' => [
                    'count_users' => count($line4_ids),
                    'count_shares' => $line4_count_shares,
                ],
                'line5' => [
                    'count_users' => count($line5_ids),
                    'count_shares' => $line5_count_shares,
                ],
            ],
            'history' => ReportReferral::where('member_id', $user->id)
                ->where('type', 'shareholding')
                ->orderBy('id', 'desc')
                ->limit(100)
                ->get(),
            'sum_history' => ReportReferral::where('member_id', $user->id)
                ->where('type', 'shareholding')
                ->sum('sum'),
        ];
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $response
        );
    }
}
