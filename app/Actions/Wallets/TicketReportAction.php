<?php

namespace App\Actions\Wallets;

use App\Models\TicketReport;

class TicketReportAction
{
    public static function create($user_id, $count, $type, $comment = null)
    {
        $ticketReport = new TicketReport();
        $ticketReport->user_id = $user_id;
        $ticketReport->count = $count;
        $ticketReport->type = $type;
        $ticketReport->comment = $comment;
        $ticketReport->save();
    }
}
