<?php

namespace App\Http\Controllers\V1;

use App\Models\ArbDeposit;
use App\Models\ReportReferral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CapitalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hystorys = ReportReferral::where('member_id', $user->id)->orderBy('created_at', 'desc')->limit(100)->get();
        foreach ($hystorys as $hystory) {
            $hystory['user_from'] = User::where('id', $hystory->owner_id)->first() ?? null;
        }

        return response([
            'balance' => [
                'wallet' => $user->wallet,
                'hystory' => $hystorys,
            ],
            'summary' => [
                'top_index' => 0,
                'arb' => ArbDeposit::where('user_id', $user->id)->where('is_active', true)->where('is_request', false)->sum('amount'),
                'private' => $user->token_private,
            ],
        ]);
    }
}
