<?php

namespace App\Http\Controllers\V1;

use App\Models\Report;
use App\Models\RoundType;
use App\Models\UserAccount;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class StatisticController extends Controller
{
    public function getStatistic() {

        $account = UserAccount::where('user_id', Auth::user()->id)
            ->where('available', true)
            ->where('active', true)
            ->where(function ($query) {
                $round_types = RoundType::where('queue', Auth::user()->active_queue)->get();
                foreach($round_types as $round_type) {
                    $query->orWhere('next_round', $round_type->id);
                }
            })
            ->first();

        if($account) {
            $sents = Report::where('from_id', $account->id)->where('amount', '>', 1)->get();
            $arr1 = [];
            foreach ($sents as $sent) {
                $arr1[$sent->id] = $sent->round_id;
            }
            $arr1 = array_unique($arr1);
            $sent = 0;
            foreach($arr1 as $k=>$arr) {
                $sent +=  Report::where('id', $k)->sum('amount');
            }

            $receiveds = Report::where('to_id', $account->id)->where('amount', '>', 1)->get();
            $arr2 = [];
            foreach ($receiveds as $received) {
                $arr2[$received->id] = $received->from_id;
            }
            $arr2 = array_unique($arr2);
            $received = 0;
            foreach($arr2 as $k=>$arr) {
                $received +=  Report::where('id', $k)->sum('amount');
            }
        }

//        $account = UserAccount::join('round_types', 'user_accounts.next_round', '=', 'round_types.id')
//            ->where('user_accounts.user_id', Auth::user()->id)
//            ->where('user_accounts.available', true)
//            ->where('user_accounts.active', true)
//            ->where('round_types.queue', Auth::user()->active_queue)
//            ->first();
//
//        if($account) {
//            $sent = Report::where('from_id', $account->id)->sum('amount');
//            $received = Report::where('to_id', $account->id)->sum('amount');
//        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'sent' => $sent ?? 0 ,
                'received' => $received ?? 0,
            ]
        );
    }
}
