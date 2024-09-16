<?php

namespace App\Http\Controllers\V1;

use App\Models\Training;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function showTraining() {
        $training = Training::find(1);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'training' => $training,
            ]
        );
    }

    public function update(Request $request) {
        $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $training = Training::find(1);
        $training->title = $request->get('title');
        $training->description = $request->get('description');
        $training->save();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'training' => $training,
            ]
        );
    }
}
