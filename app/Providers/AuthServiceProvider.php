<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\ScheduleCourse;
use App\Models\LessonRecord;
use App\Policies\ScheduleCoursePolicy;
use App\Policies\LessonRecordPolicy;
use App\Policies\AcademyCourseCategoryPolicy;
use App\Models\AcademyCourseCategory;

use App\Policies\AcademyCoursePolicy;
use App\Models\AcademyCourse;

use App\Policies\AcademyCourseItemPolicy;
use App\Models\AcademyCourseItem;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ScheduleCourse::class => ScheduleCoursePolicy::class,
        LessonRecord::class => LessonRecordPolicy::class,
        AcademyCourseCategory::class => AcademyCourseCategoryPolicy::class,
        AcademyCourse::class => AcademyCoursePolicy::class,
        AcademyCourseItem::class => AcademyCourseItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Sanctum::getAccessTokenFromRequestUsing(function (Request $request) {
            return $request->cookie('access_token');
        });
    }
}
