<?php

namespace App\Http\Controllers\V1;

use App\Models\AcademyCourse;
use App\Models\AcademyCourseCategory;
use App\Models\AcademyCourseCategorySaved;
use App\Models\AcademyPayed;
use App\Models\AcademySubscribe;
use App\Models\BanerAcademy;
use App\Models\BannerPayed;
use App\Models\GiftClub;
use App\Models\LessonRecord;
use App\Models\ScheduleCourse;
use App\Models\TicketReport;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AcademyCoursesController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyCourse::class,'course');
    }

    public function getAcademyMain(Request $request)
    {
        $user = Auth::user();
        $saves = AcademyCourseCategorySaved::where(['user_id' => $user->id])->get();
        $saved_sections = [];

        if(count($saves) > 0) {
            foreach($saves as $saved) {
                $saved_sections[] = AcademyCourseCategory::where(['id' => $saved->category_id])->first();
            }
        }

        $categories = AcademyCourseCategory::get();
        foreach($categories as $category) {
            $category['is_saved'] = (bool) AcademyCourseCategorySaved::where([
                    'user_id' => $user->id,
                    'category_id' => $category->id]
            )->first();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                "banner" => BanerAcademy::where(['id'=>1])->first(),
                "sections" => $categories,
                "schedule_courses" => ScheduleCourse::get(),
                "lessons_record" => LessonRecord::get(),
                "new_lesson_records" => GiftClub::where('is_actual', true)->orderBy('id', 'desc')->get(),
                "saved_sections" => $saved_sections,
            ]
        );
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            AcademyCourse::where([
                'academy_course_category_id' => $validated['id']
            ])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'academy_course_category_id' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'description' => ['string'],
            'gift' => ['required', 'string'],
            'price' => ['required', 'integer'],
            'tokens' => ['required', 'integer'],
            'subscription_cost' => ['integer', 'nullable'],
            'subscription_cost_first' => ['integer', 'nullable'],
        ]);
        $academyCourse = AcademyCourse::create([
            'name' => $validated['name'],
            'academy_course_category_id' => $validated['academy_course_category_id'],
            'type' => $validated['type'],
            'type_translated' => Str::slug($validated['type']),
            'description' => $validated['description'] ?? null,
            'gift' => $validated['gift'],
            'price' => $validated['price'],
            'tokens' => $validated['tokens'],
            'subscription_cost' => $validated['subscription_cost'] ?? null,
            'subscription_cost_first' => $validated['subscription_cost_first'] ?? null,
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $academyCourse
        );
    }

    public function show(AcademyCourse $course)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $course
        );
    }

    public function update(AcademyCourse $course, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'academy_course_category_id' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'description' => ['string'],
            'gift' => ['required', 'string'],
            'price' => ['required', 'integer'],
            'tokens' => ['required', 'integer'],
            'subscription_cost' => ['integer', 'nullable'],
            'subscription_cost_first' => ['integer', 'nullable'],
        ]);

        $course = $course->update([
            'name' => $validated['name'],
            'academy_course_category_id' => $validated['academy_course_category_id'],
            'type' => $validated['type'],
            'type_translated' => Str::slug($validated['type']),
            'description' => $validated['description'] ?? null,
            'gift' => $validated['gift'],
            'price' => $validated['price'],
            'tokens' => $validated['tokens'],
            'subscription_cost' => $validated['subscription_cost'] ?? null,
            'subscription_cost_first' => $validated['subscription_cost_first'] ?? null,
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $course
        );
    }

    public function destroy(AcademyCourse $course)
    {
        $course->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }

    public function isCoursePayed(Request $request)
    {
        $validated = $request->validate([
            'academy_course_id' => ['required', 'integer'],
        ]);

        if ($validated['academy_course_id'] == 49) {
            $course = BannerPayed::where('product_id', 'banner_academy_1678695067500')
                ->where('user_id', auth()->user()->id)
                ->first();
        }elseif ($validated['academy_course_id'] == 54) {
            $course = BannerPayed::where('product_id', 'banner_academy_1681102414973')
                ->where('user_id', auth()->user()->id)
                ->first();
        }
        else {
            $course = AcademyPayed::where('user_id', Auth::user()->id)->where('academy_course_id', $validated['academy_course_id'])->first();
        }

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            (bool) $course
        );
    }


    public function isPayedSub(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        $sub = AcademySubscribe::where('user_id', $user->id)->where('academy_course_id', $validated['course_id'])->orderBy('created_at', 'desc')->first();

        if(isset($sub) && $sub->is_active) {
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                [
                    'is_payed' => true,
                    'end_date' => TicketReport::where('user_id', $user->id)
                            ->where('type', 'accademy_course_sub_' . $validated['course_id'])
                            ->orderBy('created_at', 'desc')
                            ->first()->created_at->addMonth() ?? null
                ]
            );
        }
        else {
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                [
                    'is_payed' => false,
                    'end_date' => null
                ]
            );
        }
    }
}
