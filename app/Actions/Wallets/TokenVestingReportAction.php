<?php

namespace App\Actions\Wallets;

use App\Models\TokenVestingReport;

class TokenVestingReportAction
{
    public static function create($user_id, $count, $type)
    {
        $ticketReport = new TokenVestingReport();
        $ticketReport->user_id = $user_id;
        $ticketReport->count = $count;
        $ticketReport->type = $type;
        $ticketReport->save();
    }
}
