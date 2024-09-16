<?php

namespace App\Http\Controllers\V1;

use App\Models\PoolPercent;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class PoolPercentController extends Controller
{
    public function index()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            PoolPercent::all()
        );
    }

    public function show(PoolPercent $poolPercent)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $poolPercent
        );
    }

    public function update(Request $request, PoolPercent $poolPercent)
    {
        $validated = $request->validate([
            'percent' => 'required|numeric',
        ]);

        $poolPercent->update([
            'percent' => $validated['percent'],
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $poolPercent
        );
    }
}
