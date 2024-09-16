<?php

namespace App\Http\Controllers\V1;

use App\Models\LessonRecord;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class LessonRecordController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LessonRecord::class, 'lesson_record');
    }

    public function index()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            LessonRecord::get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'date' => ['required', 'string'],
            'link' => ['required', 'string'],
            'price' => ['required', 'numeric'],
        ]);

        $LessonRecord = LessonRecord::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'link' => $validated['link'],
            'price' => $validated['price'],
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $LessonRecord
        );
    }

    public function show(LessonRecord $LessonRecord)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $LessonRecord
        );
    }

    public function update(LessonRecord $lesson_record, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'date' => ['required', 'string'],
            'link' => ['required', 'string'],
            'price' => ['required', 'numeric'],
        ]);
        $lesson_record = $lesson_record->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'link' => $validated['link'],
            'price' => $validated['double'],
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $lesson_record
        );
    }

    public function destroy(LessonRecord  $lesson_record)
    {
        $lesson_record->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }
}
