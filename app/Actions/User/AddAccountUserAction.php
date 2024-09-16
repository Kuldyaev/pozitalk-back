<?php
namespace App\Actions\User;

use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddAccountUserAction
{
    public static function add($accounts)
    {
        foreach($accounts as $account) {
            $account->active = false;
            $account->save();
        }

        $user_account = new UserAccount();
        $user_account->user_id = Auth::user()->id;
        $user_account->active = true;
        $user_account->role_id = 1;
        $user_account->next_round = Auth::user()->active_queue;
        $user_account->circle = 1;
        $user_account->number = count($accounts) + 1;
        $user_account->save();

        return $user_account;
    }
}
