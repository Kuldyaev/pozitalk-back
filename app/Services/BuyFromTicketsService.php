<?php

namespace App\Services;

use App\Actions\Wallets\TicketReportAction;
use App\Models\ArbBalance;

class BuyFromTicketsService
{
    public function arb_pay($user, $count): array
    {
        $user->count_avatars -= $count;
        $user->save();

        TicketReportAction::create($user->id, $count, 'arb_pay');

        $arbUser = ArbBalance::where('user_id', $user->id)->first();
        $arbUser->can_pay += $count * 1000;
        $arbUser->save();

        return ['can_pay' => $arbUser->can_pay];
    }
}
