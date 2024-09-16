<?php

namespace App\Http\Controllers\V1;

use App\Models\AcademyCourseItem;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;

class AcademyCourseItemsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyCourseItem::class,'item');
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer'],
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            AcademyCourseItem::where([
                'academy_course_id' => $validated['category_id']
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'academy_course_id' => ['required', 'integer'],
            'link' => ['required', 'string'],
            'preview_image' => ['string'],
            'timecodes' => ['string'],
        ]);

        $AcademyCourseItem = AcademyCourseItem::create([
            'name' => $validated['name'],
            'academy_course_id' => $validated['academy_course_id'],
            'link' => $validated['link'],
            'timecodes' => $validated['timecodes'] ?? null,
            'preview_image' => $validated['preview_image'] ?? null,
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $AcademyCourseItem
        );
    }

    public function show(AcademyCourseItem $item)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $item
        );
    }

    public function update(AcademyCourseItem $item, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'academy_course_id' => ['required', 'integer'],
            'link' => ['required', 'string'],
            'preview_image' => ['string'],
            'timecodes' => ['string'],
        ]);
        $item = $item->update([
            'name' => $validated['name'],
            'academy_course_id' => $validated['academy_course_id'],
            'link' => $validated['link'],
            'timecodes' => $validated['timecodes'] ?? null,
            'preview_image' => $validated['preview_image'] ?? null,
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $item
        );
    }

    public function destroy(AcademyCourseItem $item)
    {
        $item->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }
}
