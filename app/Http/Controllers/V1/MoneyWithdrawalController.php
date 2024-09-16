<?php

namespace App\Http\Controllers\V1;

use App\Models\MoneyWithdrawal;
use App\Models\ReportReferral;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class MoneyWithdrawalController extends Controller
{

    public function index(Request $request)
    {
        $sort_by = 'id';
        $sort = 'desc';
        if ($request->filled('sortBy') && $request->filled('sort')) {
            $sort_by = $request->get('sortBy');
            $sort = $request->get('sort');
        }

        if($request->filled('searchBy') && $request->filled('query')) {
            $search_by = $request->get('searchBy');
            $querys = $request->get('query');

            $users = User::where($search_by, 'like', '%'.$querys.'%')->get();
            $user_ids = [];
            foreach ($users as $user) {
                $user_ids[] = $user->id;
            }

            $query = MoneyWithdrawal::with('user')
                ->orderBy($sort_by, $sort)
                ->whereIn('user_id', $user_ids)
                ->paginate(15);
        }
        else {
            $query = MoneyWithdrawal::with('user')
                ->orderBy($sort_by, $sort)
                ->paginate(15);
        }


        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'money_withdrawal' => $query,
            ]
        );
    }

    public function show($id)
    {
        $money_withdrawal = MoneyWithdrawal::with('user')->find($id);

        if (!$money_withdrawal) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['Money withdrawal not found.'],
                []
            );
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'money_withdrawal' => $money_withdrawal,
            ]
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string',
            'amount' => 'required|numeric',
            'coin' => 'required|string'
        ]);

        if($request->get('amount') < 50) {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['Minimum withdrawal amount is 50.'],
                []
            );
        }

        $user = auth()->user();

        if ($user->wallet < $request->get('amount')) {
            return ResponseService::sendJsonResponse(
                false,
                400,
                ['Insufficient balance.'],
                []
            );
        }

        $user->update([
            'wallet' => $user->wallet - $request->get('amount'),
        ]);

        $money_withdrawal = MoneyWithdrawal::create([
            'user_id' => $user->id,
            'wallet_address' => $request->get('wallet_address'),
            'amount' => $request->get('amount'),
            'coin' => $request->get('coin'),
        ]);

        $rep = ReportReferral::create([
            'owner_id' => 1,
            'member_id' => $user->id,
            'sum' => $request->get('amount'),
            'type' => 'money_withdrawal',
        ]);

        $rep->type = 'money_withdrawal';
        $rep->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'money_withdrawal' => $money_withdrawal,
            ]
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'integer',
        ]);

        $money_withdrawal = MoneyWithdrawal::find($id);

        if (!$money_withdrawal || $money_withdrawal->status > 1) {
            return ResponseService::sendJsonResponse(
                false,
                404,
                ['Money withdrawal not found.'],
                []
            );
        }

        $user = $money_withdrawal->user;
        if ($request->get('status') == 3) {
            $user->update([
                'wallet' => $user->wallet + $money_withdrawal->amount,
            ]);

            $rep = ReportReferral::create([
                'owner_id' => 1,
                'member_id' => $user->id,
                'sum' => $money_withdrawal->amount,
                'type' => 'money_refund',
            ]);

            $rep->type = 'money_refund';
            $rep->save();
        }
        $money_withdrawal->update($request->all());

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'money_withdrawal' => $money_withdrawal,
            ]
        );
    }
}
