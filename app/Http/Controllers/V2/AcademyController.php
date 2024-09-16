<?php

namespace App\Http\Controllers\V2;

use Vi\Actions\Academy\AcademyCategorySetLatestAction;
use App\Http\Controllers\V2\Controller;
use App\Models\AcademyCourse;
use App\Models\AcademyCourseCategory;
use App\Models\AcademyCourseCategorySaved;
use App\Models\GiftClub;
use App\Models\ScheduleCourse;
use App\Models\TicketReport;
use App\Models\UsdtTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;

class AcademyController extends Controller
{
    #[Get(
        path: "/academy/exclusive",
        description: "Эксклюзив для статусов.",
        tags: ["Academy"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function exclusive(): JsonResponse
    {
        $user = auth()->user();
        $courses = AcademyCourseCategory::where('exclusive', '!=', null)
            ->orderByDesc('sort_order')
            ->get();

        if ($user->founder_status == 0) {
            $userStatus = UsdtTransaction::where('user_id', $user->id)
                ->where(function ($query) {
                    $query->where('product', 'bronze')
                        ->orWhere('product', 'silver')
                        ->orWhere('product', 'gold')
                        ->orWhere('product', 'platinum');
                })
                ->orderBy('id', 'desc')
                ->first()->product ?? 'base';
        } else {
            $userStatus = 'founder' . $user->founder_status;
        }

        foreach ($courses as $category) {
            $category['is_available'] = (bool) AcademyCourseCategory::IS_AVAILABLE_STATUS[$userStatus] >= AcademyCourseCategory::IS_AVAILABLE_STATUS[$courses->exclusive];
        }

        return response()->json([
            'success' => true,
            'message' => 'Эксклюзив для статусов.',
            'data' => $courses
        ]);
    }

    #[Get(
        path: "/academy/relevant",
        description: "Актуальные записи.",
        tags: ["Academy"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function relevant(): JsonResponse
    {
        $courses = GiftClub::where('is_actual', true)->orderBy('id', 'desc')->get();

        foreach ($courses as $course) {
            $course->date = Carbon::parse($course->date)->unix();
        }

        return response()->json([
            'success' => true,
            'message' => 'Актуальные записи.',
            'data' => $courses
        ]);
    }

    #[Get(
        path: "/academy/other",
        description: "Другие разделы академии.",
        tags: ["Academy"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function other(): JsonResponse
    {
        $courses = AcademyCourseCategory::where('exclusive', '=', null)
            ->orderByDesc('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Другие разделы академии.',
            'data' => $courses
        ]);
    }

    #[Get(
        path: "/academy/schedule",
        description: "Расписание.",
        tags: ["Academy"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function schedule(): JsonResponse
    {
        $courses = ScheduleCourse::get();

        foreach ($courses as $course) {
            $course->date = Carbon::parse($course->date)->unix();
        }

        return response()->json([
            'success' => true,
            'message' => 'Расписание.',
            'data' => $courses
        ]);
    }

    #[Get(
        path: "/academy/categories/{id}",
        description: "Детальная категория.",
        tags: ["Academy"],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function showCategory(AcademyCourseCategory $category): JsonResponse
    {
        if ($category->test) {
            $report = TicketReport::where('user_id', Auth::user()->id)
                ->where('type', 'test_course')
                ->first();
            $category['test_payed'] = isset($report);
        }

        (new AcademyCategorySetLatestAction())->run(Auth::user(), $category->id);

        return response()->json([
            'success' => true,
            'message' => 'Категория.',
            'data' => $category
        ]);
    }

    #[Get(
        path: "/academy/course",
        description: "Детальная категории курсов. ID категории передавать.",
        tags: ["Academy"],
        parameters: [
            new Parameter(
                name: 'id',
                in: 'query',
                example: '2',
            )
        ],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(),
            )
        ]
    )]
    public function showCourse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        (new AcademyCategorySetLatestAction())->run(Auth::user(), $validated['id']);

        return response()->json([
            'success' => true,
            'message' => 'Курс.',
            'data' => AcademyCourse::where([
                'academy_course_category_id' => $validated['id']
            ])->get()
        ]);
    }
}
