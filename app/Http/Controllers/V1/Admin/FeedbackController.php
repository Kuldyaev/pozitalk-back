<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\V1\Controller;
use App\Models\Feedback;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'image' => 'file:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $feedback = Feedback::create([
            'message' => $request->get('message'),
            'image' => $request->file('image')->store('feedback')
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'feedback' => $feedback
            ]
        );
    }
}
