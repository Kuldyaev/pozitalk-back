<?php

namespace App\Http\Controllers\V1;

use App\Models\TokenRate;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class TokenRateController extends Controller
{
    public function show()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            TokenRate::first()
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'private_rate' => ['required', 'numeric'],
            'classic_rate' => ['required', 'numeric'],
        ]);

        $token = TokenRate::first()->update([
            'private_rate' => $validated['private_rate'],
            'classic_rate' => $validated['classic_rate'],
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $token
        );
    }
}
