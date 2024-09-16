<?php

namespace App\Http\Resources\Academy;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

class CourceItemFileResource extends JsonResource
{
    #[Schema(
        title: "AcademyCourseItemFileResource",
        schema: "AcademyCourseItemFileResource",
        properties: [
            new Property(
                property: 'id',
                type: 'integer',
            ),
            new Property(
                property: 'file',
                type: 'string',
            ),
            new Property(
                property: 'url',
                type: 'string',
            ),
            new Property(
                property: 'type',
                type: 'string',
            ),
            new Property(
                property: 'name',
                type: 'string',
            )
        ]
    )]
    public function toArray($request)
    {
        return [
            'url' => $this->url,
        ] + parent::toArray($request);
    }
}
