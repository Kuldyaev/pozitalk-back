<?php

namespace App\Http\Controllers\V1;

use App\Models\IndexTokenInfo;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class IndexTokenInfoController extends Controller
{
    public function update(int $id, Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'icon' => 'nullable|string',
        ]);

        $indexTokenInfo = IndexTokenInfo::findOrFail($id);
        $indexTokenInfo->title = $request->get('title');
        $indexTokenInfo->icon = $request->get('icon');
        $indexTokenInfo->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexTokenInfo
        );
    }

    public function index()
    {
        $indexTokenInfo = IndexTokenInfo::all();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexTokenInfo
        );
    }

    public function show(int $id)
    {
        $indexTokenInfo = IndexTokenInfo::findOrFail($id);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $indexTokenInfo
        );
    }
}
