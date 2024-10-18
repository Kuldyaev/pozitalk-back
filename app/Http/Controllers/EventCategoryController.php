<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Http\Requests\EventCategoryRequest;
use App\Queries\EventCategoryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

class EventCategoryController extends Controller
{

    public function index(): JsonResponse
    {
        //Получить все events categories
        $eventCategory=EventCategory::all();
         return response()->json($eventCategory);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    #[Get(
        path: '/events/categories',
        operationId: 'getAllEventsCategory',
        tags: ['Events'],
        security: [['bearerAuth' => []]],
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(
                    ref: '#/components/schemas/EventCategory',
                    type: 'object',
                )
            )
        ]
    )]
    public function list(): JsonResponse
    {
        //Получить все events categories
        $eventCategory=EventCategory::all();
         return response()->json($eventCategory);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(EventCategoryRequest $request, EventCategoryBuilder $builder)
    {
        //
        $event_category = $request->validated();

        $categorycreated = $builder->create($event_category);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventCategory  $eventCategory
     * @return \Illuminate\Http\Response
     */
    public function show(EventCategory $eventCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventCategory  $eventCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(EventCategory $eventCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EventCategory  $eventCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventCategory  $eventCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventCategory $eventCategory)
    {
        //
    }
}
