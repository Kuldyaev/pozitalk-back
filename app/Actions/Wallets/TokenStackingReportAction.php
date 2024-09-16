<?php

namespace App\Actions\Wallets;

use App\Models\TokenStackingReport;

class TokenStackingReportAction
{
    public static function create($user_id, $count, $type)
    {
        $ticketReport = new TokenStackingReport();
        $ticketReport->user_id = $user_id;
        $ticketReport->count = $count;
        $ticketReport->type = $type;
        $ticketReport->save();
    }
}
