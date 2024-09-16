<?php
namespace App\Actions\Round;

use App\Models\RoundGiver;
use App\Models\RoundGiverPay;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewGiverAction
{
    public static function store($round_id, $account, $cel = false)
    {
        if ($cel == true) {
            $pay = RoundGiverPay::where('round_id', $round_id)->where('giver_id', null)->first();
            if(!$pay) {
                $pay = new RoundGiverPay();
                $pay->round_id = $round_id;
                $pay->is_payed = false;
            }

            $giver = new RoundGiver();
            $giver->round_id = $round_id;
            $giver->status_id = $pay->is_payed == false ? 8 : 2;
            $giver->account_id = $account->id;
            $giver->start = $round_id != null ? Carbon::now() : null;
            $giver->round_type_id = $account->next_round;
            $giver->is_distributed = false;
            $giver->is_congratulated = false;
            $giver->save();

            $pay->giver_id = $giver->id;
            $pay->save();
        }
        else {
            $giver = new RoundGiver();
            $giver->round_id = $round_id;
            $giver->status_id = $round_id != null ? 2 : 1;
            $giver->account_id = $account->id;
            $giver->start = $round_id != null ? Carbon::now() : null;
            $giver->round_type_id = $account->next_round;
            $giver->is_distributed = false;
            $giver->is_congratulated = false;
            $giver->save();
        }

        return $giver;
    }
}
