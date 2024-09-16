<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Resources\Academy\AcademyCourseItemMomentResource;
use App\Models\AcademyCourseItem;
use Vi\Models\Academy\AcademyCourseItemMoment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class AcademyCourseItemsMomentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyCourseItem::class, 'item');
    }

    #[Get(
        tags: ['Admin.Academy.Course.Item.Moment'],
        operationId: 'adminGetAcademyCourseItemMoments',
        path: '/academy/item/{item}/moment',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса'),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'Ok',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'array',
                        items: new Items(ref: '#/components/schemas/AcademyCourseItemMomentResource')
                    )
                ])
            )
        ]
    )]
    public function index(Request $request, AcademyCourseItem $item): JsonResource
    {
        return AcademyCourseItemMomentResource::collection($item->moments);
    }

    #[Post(
        tags: ['Admin.Academy.Course.Item.Moment'],
        operationId: 'adminCreateAcademyCourseItemMoments',
        path: '/academy/item/{item}/moment',
        parameters: [new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса')],
        requestBody: new RequestBody(content: new JsonContent(properties: [
            new Property(
                property: 'moment',
                type: 'string',
                format: 'binary',
            ),
            new Property(
                property: 'name',
                type: 'string',
            ),
            new Property(
                property: 'type',
                type: 'string',
            )
        ])),
        responses: [
            new Response(
                response: 201,
                description: 'Created',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'object',
                        ref: '#/components/schemas/AcademyCourseItemMomentResource'
                    )
                ])
            ),
        ]
    )]
    public function store(Request $request, AcademyCourseItem $item)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'caption' => 'string',
            'link' => 'required|string',
        ]);

        $AcademyCourseItemMoment = $item->moments()->create(
            $validated
        );

        return new AcademyCourseItemMomentResource($AcademyCourseItemMoment);
    }

    #[Get(
        tags: ['Admin.Academy.Course.Item.Moment'],
        operationId: 'adminShowAcademyCourseItemMoment',
        path: '/academy/item/{item}/moment/{moment}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор куса'),
            new Parameter(name: 'moment', in: 'path', description: 'Идентификатор файла'),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'Ok',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'object',
                        ref: '#/components/schemas/AcademyCourseItemMomentResource'
                    )
                ])
            )
        ]
    )]
    public function show(AcademyCourseItem $item, AcademyCourseItemMoment $moment): JsonResource
    {
        return new AcademyCourseItemMomentResource($moment);
    }

    #[Patch(
        tags: ['Admin.Academy.Course.Item.Moment'],
        operationId: 'adminUpdateAcademyCourseItemMoment',
        path: '/academy/item/{item}/moment/{moment}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса'),
            new Parameter(name: 'moment', in: 'path', description: 'Идентификатор файла'),
        ],
        requestBody: new RequestBody(content: new JsonContent(properties: [
            new Property(
                property: 'name',
                type: 'string',
            ),
            new Property(
                property: 'type',
                type: 'string',
            )
        ])),
        responses: [
            new Response(
                response: 200,
                description: 'Ok',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'object',
                        ref: '#/components/schemas/AcademyCourseItemMomentResource'
                    )
                ])
            )
        ]
    )]
    public function update(
        Request $request,
        AcademyCourseItem $item,
        AcademyCourseItemMoment $moment
    ): AcademyCourseItemMomentResource {
        $validated = $request->validate([
            'title' => 'string',
            'caption' => 'string',
            'link' => 'string',
        ]);

        $moment->update($validated);

        return new AcademyCourseItemMomentResource($moment);
    }

    #[Delete(
        tags: ['Admin.Academy.Course.Item.Moment'],
        operationId: 'adminDeleteAcademyCourseItemMoment',
        path: '/academy/item/{item}/moment/{moment}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса'),
            new Parameter(name: 'moment', in: 'path', description: 'Идентификатор файла'),
        ],
        responses: [
            new Response(
                response: 204,
                description: 'No Content'
            ),
            new Response(
                response: 200,
                description: 'OK'
            )
        ]
    )]
    public function destroy(AcademyCourseItem $item, AcademyCourseItemMoment $moment): void
    {
        $moment->delete();
    }
}
