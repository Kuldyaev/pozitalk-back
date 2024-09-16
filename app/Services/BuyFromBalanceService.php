<?php

namespace App\Services;

use App\Models\ArbDeposit;
use App\Models\IaSystemDeposit;
use App\Models\ReportReferral;
use App\Models\UsdtTransaction;
use Carbon\Carbon;

class BuyFromBalanceService
{
    public function arb_pay($user, $count_months, $amount): array
    {
        $user->wallet -= $amount;
        $user->save();

        $rep = ReportReferral::create([
            'owner_id' => 1,
            'member_id' => $user->id,
            'sum' => $amount,
            'type' => 'ia_system_deposit',
        ]);
        $rep->type = 'ia_system_deposit';
        $rep->save();

        IaSystemDeposit::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'count_months' => $count_months,
        ]);

        UsdtTransaction::create([
            'user_id' => $user->id,
            'transaction_id' => 'balance',
            'sum_usd' => $amount,
            'address' => 'balance',
            'product' => 'ia_system_deposit',
            'date' => Carbon::now()
        ]);

        return ['Success'];
    }
}
