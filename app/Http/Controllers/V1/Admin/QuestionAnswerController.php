<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\V1\Controller;
use App\Models\QuestionAnswer;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class QuestionAnswerController extends Controller
{
    public function index()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'question_answer' => QuestionAnswer::orderBy('sort', 'asc')->get()
            ]
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|integer',
            'answer' => 'required|string',
            'sort' => 'required|string',
        ]);

        $questionAnswer = QuestionAnswer::create($request->all());

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'question_answer' => $questionAnswer
            ]
        );
    }

    public function show(QuestionAnswer $questionAnswer)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'question_answer' => $questionAnswer
            ]
        );
    }

    public function update(Request $request, QuestionAnswer $questionAnswer)
    {
        $request->validate([
            'question' => 'required|integer',
            'answer' => 'required|string',
            'sort' => 'required|string',
        ]);

        $questionAnswer->update($request->all());

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'question_answer' => $questionAnswer
            ]
        );
    }

    public function destroy(QuestionAnswer $questionAnswer)
    {
        $questionAnswer->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            ['Success']
        );
    }
}
