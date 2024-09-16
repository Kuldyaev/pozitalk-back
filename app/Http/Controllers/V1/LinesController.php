<?php

namespace App\Http\Controllers\V1;

use App\Models\RoundType;
use App\Models\UserAccount;
use App\Services\Response\ResponseService;
use Illuminate\Support\Facades\Auth;

class LinesController extends Controller
{
    public function getLines() {
        $roundTypes = RoundType::all();
        $result = [];

        foreach ($roundTypes as $roundType) {
            $queue = $roundType->queue;

            if (!isset($result[$queue])) {
                $result[$queue] = [];
            }

            $res = true;
            if($roundType->id > 4) {
                $res = UserAccount::whereHas('rounds', function($query) {
                    $query->where('round_type_id', 2)->where('active', false);
                })
                    ->where('user_id', Auth::user()->id)
                    ->exists();
            }

            $result[$queue][] = [
                'id' => $roundType->id,
                'price' => $roundType->price,
                'count_rounds' => $roundType->count_rounds,
                'count_givers' => $roundType->count_givers,
                'queue' => $roundType->queue,
                'count' => UserAccount::where('next_round', $roundType->id)->where('role_id', '!=', 1)->where('available', true)->count(),
                'available' => $res || Auth::user()->role_id == 4,
                'is_need_pay' => $roundType->is_need_pay,
            ];
        }

        $res = [];
        foreach ($result as $value) {
            $res[] = $value;
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'lines' => $res
            ]
        );
    }
}
