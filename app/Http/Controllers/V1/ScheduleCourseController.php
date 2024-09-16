<?php

namespace App\Http\Controllers\V1;

use App\Models\AcademyCourseCategory;
use App\Models\AcademySubscribe;
use App\Models\ScheduleCourse;
use App\Models\TicketReport;
use App\Services\Response\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleCourseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ScheduleCourse::class, 'schedule_course');
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'is_capital' => ['boolean', 'nullable'],
            'from_admin' => ['boolean', 'nullable'],
            'is_partner' => ['boolean', 'nullable'],
        ]);

        if(isset($validated['is_partner']) && $validated['is_partner'] == true) {
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                ScheduleCourse::where('is_partner', true)->get()
            );
        }

        if(isset($validated['from_admin']) && $validated['from_admin']) {
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                ScheduleCourse::all()
            );
        }

        if(isset($validated['is_capital'])) {
            $user = Auth::user();
            $capitalCourse = AcademyCourseCategory::where('is_capital', true)->first();
            $sub = AcademySubscribe::where('user_id', $user->id)->where('academy_course_category_id', $capitalCourse->id)->first();
            $bigCapital = TicketReport::where('user_id', $user->id)->where('type', 'big_capital')->first();

            if(isset($bigCapital) && isset($sub) && $sub->is_active) {
                $shedules = ScheduleCourse::where('is_capital', true)->get();
            }
            else if(isset($sub) && $sub->is_active) {
                $shedules = ScheduleCourse::where('is_capital', true)->where('is_big_capital', false)->get();
            }
            else {
                $shedules = ScheduleCourse::where('is_capital', true)->where('is_for_subscribers', false)->get();
            }

            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                $shedules
            );
        }
        else {
            return ResponseService::sendJsonResponse(
                true,
                200,
                [],
                ScheduleCourse::where('is_capital', false)->where('is_big_capital', false)->get()
            );
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'date' => ['required', 'string'],
            'link' => ['required', 'string'],
            'is_capital' => ['boolean'],
            'is_for_subscribers' => ['boolean'],
            'is_big_capital' => ['boolean'],
            'is_partner' => ['boolean'],
        ]);

        $scheduleCourse = ScheduleCourse::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'link' => $validated['link'],
            'is_capital' => $validated['is_capital'] ?? false,
            'is_for_subscribers' => $validated['is_for_subscribers'] ?? false,
            'is_big_capital' => $validated['is_big_capital'] ?? false,
            'is_partner' => $validated['is_partner'] ?? false,
        ]);

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $scheduleCourse
        );
    }

    public function show(ScheduleCourse $schedule_course)
    {
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $schedule_course
        );
    }

    public function update(ScheduleCourse $schedule_course, Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'date' => ['required', 'string'],
            'link' => ['required', 'string'],
            'is_capital' => ['boolean'],
            'is_for_subscribers' => ['boolean'],
            'is_big_capital' => ['boolean'],
            'is_partner' => ['boolean'],
        ]);
        $schedule_course = $schedule_course->update([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'link' => $validated['link'],
            'is_capital' => $validated['is_capital'] ?? false,
            'is_for_subscribers' => $validated['is_for_subscribers'] ?? false,
            'is_big_capital' => $validated['is_big_capital'] ?? false,
            'is_partner' => $validated['is_partner'] ?? false,
        ]);
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            $schedule_course
        );
    }

    public function destroy(ScheduleCourse $schedule_course)
    {
        $schedule_course->delete();
        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            'success'
        );
    }
}
