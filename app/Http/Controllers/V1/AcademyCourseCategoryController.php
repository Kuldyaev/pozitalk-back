<?php

namespace App\Http\Controllers\V1;

use App\Models\AcademyCourseCategory;
use App\Models\AcademyCourseCategorySaved;
use App\Models\TicketReport;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AcademyCourseCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyCourseCategory::class, 'category');
    }

    public function index()
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            AcademyCourseCategory::orderBy('sort_order')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'tags' => ['array', 'nullable'],
            'zoom' => ['boolean'],
            'test' => ['boolean'],
            'date' => ['string', 'nullable'],
            'url' => ['string', 'nullable'],
            'is_cycle' => ['boolean', 'nullable'],
            'is_deutsche' => ['boolean', 'nullable'],
            'is_capital' => ['boolean', 'nullable'],
            'direction' => ['string', 'nullable'],
            'short_description' => ['string', 'nullable'],
            'access' => ['string', 'nullable'],
            'image' => ['string', 'nullable'],
            'sort_order' => ['string', 'nullable'],
        ]);
        $academyCourseCategory = AcademyCourseCategory::create([
            'name' => $validated['name'],
            'tags' => $validated['tags'],
            'zoom' => $validated['zoom'] ?? false,
            'test' => $validated['test'] ?? false,
            'date' => $validated['date'] ?? null,
            'url' => $validated['url'] ?? null,
            'is_cycle' => $validated['is_cycle'] ?? false,
            'is_deutsche' => $validated['is_deutsche'] ?? false,
            'is_capital' => $validated['is_capital'] ?? false,
            'direction' => $validated['direction'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'access' => $validated['access'] ?? null,
            'image' => $validated['image'] ?? null,
            'sort_order' => $validated['sort_order'] ?? null,
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $academyCourseCategory
        );
    }

    public function show(AcademyCourseCategory $category)
    {
        if($category->test == true) {
            $report = TicketReport::where('user_id', Auth::user()->id)
                ->where('type', 'test_course')
                ->first();
            $category['test_payed'] = isset($report);
        }
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $category
        );
    }

    public function update(Request $request, AcademyCourseCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'tags' => ['array', 'nullable'],
            'zoom' => ['boolean'],
            'test' => ['boolean'],
            'date' => ['string', 'nullable'],
            'url' => ['string', 'nullable'],
            'is_cycle' => ['boolean', 'nullable'],
            'is_deutsche' => ['boolean', 'nullable'],
            'is_capital' => ['boolean', 'nullable'],
            'direction' => ['string', 'nullable'],
            'short_description' => ['string', 'nullable'],
            'access' => ['string', 'nullable'],
            'image' => ['string', 'nullable'],
            'sort_order' => ['string', 'nullable'],
        ]);
        $category = $category->update([
            'name' => $validated['name'],
            'tags' => $validated['tags'],
            'zoom' => $validated['zoom'] ?? false,
            'test' => $validated['test'] ?? false,
            'date' => $validated['date'] ?? null,
            'url' => $validated['url'] ?? null,
            'is_cycle' => $validated['is_cycle'] ?? false,
            'is_deutsche' => $validated['is_deutsche'] ?? false,
            'is_capital' => $validated['is_capital'] ?? false,
            'direction' => $validated['direction'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'access' => $validated['access'] ?? null,
            'image' => $validated['image'] ?? null,
            'sort_order' => $validated['sort_order'] ?? null,
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $category
        );
    }

    public function destroy(AcademyCourseCategory $category)
    {
        $category->delete();

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }

    public function saved(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);
        $category = AcademyCourseCategory::findOrFail($validated['id']);
        $user = Auth::user();
        $saved = AcademyCourseCategorySaved::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->first();

        if($saved) {
            $saved->delete();
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                'success'
            );
        } else {
            AcademyCourseCategorySaved::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
            ]);
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                'success'
            );
        }
    }
}
