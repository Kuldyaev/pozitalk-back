<?php

namespace App\Http\Controllers\V1;

use App\Models\DexnetRequest;
use App\Models\UsdtTransaction;
use App\Models\User;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class DexnetRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'region' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'address' => 'required|string',
        ]);

        $validated['user_id'] = auth()->user()->id;

        DexnetRequest::create($validated);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            true
        );
    }

    public function getUser()
    {
        $dexnet_pay = UsdtTransaction::where('user_id', auth()->user()->id)->where('product', 'dexnet')->first();
        $dexnet_request = DexnetRequest::where('user_id', auth()->user()->id)->first();

        $request = [
            'dexnet_pay' => (bool) $dexnet_pay,
            'dexnet_request' => (bool) $dexnet_request,
            'dexnet_request_address' => $dexnet_request
        ];

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $request
        );
    }

    public function index()
    {
        $requests = UsdtTransaction::where('product', 'dexnet')->orderBy('created_at', 'desc')->get();

        foreach ($requests as $request) {
            $request['user'] = User::where('id', $request->user_id)->first();
            $request['request'] = DexnetRequest::where('user_id', $request->user_id)->first();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $requests
        );
    }

    public function approve($id)
    {
        $request = DexnetRequest::findOrFail($id);
        $request->is_approved = true;
        $request->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $request
        );
    }
}
