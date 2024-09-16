<?php

namespace App\Http\Controllers\V1;

use App\Http\Resources\Academy\CourceItemFileResource;
use Vi\Models\Academy\AcademyCourseItemFile;
use App\Models\AcademyCourseItem;
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

class AcademyCourseItemsFileController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(AcademyCourseItem::class, 'item');
    }

    #[Get(
        tags: ['Admin.Academy.Course.Item.File'],
        operationId: 'adminGetAcademyCourseItemFiles',
        path: '/academy/item/{item}/file',
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
                        items: new Items(ref: '#/components/schemas/AcademyCourseItemFileResource')
                    )
                ])
            )
        ]
    )]
    public function index(Request $request, AcademyCourseItem $item): JsonResource
    {
        return CourceItemFileResource::collection($item->files);
    }

    #[Post(
        tags: ['Admin.Academy.Course.Item.File'],
        operationId: 'adminCreateAcademyCourseItemFiles',
        path: '/academy/item/{item}/file',
        parameters: [new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса')],
        requestBody: new RequestBody(content: new JsonContent(properties: [
            new Property(
                property: 'file',
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
                        ref: '#/components/schemas/AcademyCourseItemFileResource'
                    )
                ])
            ),
        ]
    )]
    public function store(Request $request, AcademyCourseItem $item)
    {
        $validated = $request->validate([
            'file' => 'required|file',
            'name' => 'string',
            'type' => 'string',
        ]);

        $AcademyCourseItemFile = $item->files()->create(
            $validated
        );

        return new CourceItemFileResource($AcademyCourseItemFile);
    }

    #[Get(
        tags: ['Admin.Academy.Course.Item.File'],
        operationId: 'adminShowAcademyCourseItemFile',
        path: '/academy/item/{item}/file/{file}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор куса'),
            new Parameter(name: 'file', in: 'path', description: 'Идентификатор файла'),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'Ok',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'object',
                        ref: '#/components/schemas/AcademyCourseItemFileResource'
                    )
                ])
            )
        ]
    )]
    public function show(AcademyCourseItem $item, AcademyCourseItemFile $file): JsonResource
    {
        return new CourceItemFileResource($file);
    }

    #[Patch(
        tags: ['Admin.Academy.Course.Item.File'],
        operationId: 'adminUpdateAcademyCourseItemFile',
        path: '/academy/item/{item}/file/{file}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса'),
            new Parameter(name: 'file', in: 'path', description: 'Идентификатор файла'),
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
                        ref: '#/components/schemas/AcademyCourseItemFileResource'
                    )
                ])
            )
        ]
    )]
    public function update(
        Request $request,
        AcademyCourseItem $item,
        AcademyCourseItemFile $file
    ): CourceItemFileResource {
        $validated = $request->validate([
            'name' => 'string',
            'type' => 'string',
        ]);

        $file->update($validated);

        return new CourceItemFileResource($file);
    }

    #[Delete(
        tags: ['Admin.Academy.Course.Item.File'],
        operationId: 'adminDeleteAcademyCourseItemFile',
        path: '/academy/item/{item}/file/{file}',
        parameters: [
            new Parameter(name: 'item', in: 'path', description: 'Идентификатор курса'),
            new Parameter(name: 'file', in: 'path', description: 'Идентификатор файла'),
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
    public function destroy(AcademyCourseItem $item, AcademyCourseItemFile $file): void
    {
        $file->delete();
    }
}
