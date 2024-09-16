<?php

namespace App\Http\Controllers\V1;

use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function canView(Request $request){
        if($request->get('referal_invited')){
            $referal = User::where('referal_invited', $request->get('referal_invited'))->first();
            if($referal) {
                return ResponseService::sendJsonResponse(
                    true,
                    200,
                    [],
                    []
                );
            }
        }
        return ResponseService::sendJsonResponse(
            false,
            200,
            [],
            []
        );

    }
}
