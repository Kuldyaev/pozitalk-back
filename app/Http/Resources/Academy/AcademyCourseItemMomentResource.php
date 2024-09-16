<?php

namespace App\Http\Resources\Academy;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

class AcademyCourseItemMomentResource extends JsonResource
{
    #[Schema(
        title: "AcademyCourseItemMomentResource",
        schema: "AcademyCourseItemMomentResource",
        properties: [
            new Property(
                property: 'id',
                type: 'integer',
            ),
            new Property(
                property: 'title',
                type: 'string',
            ),
            new Property(
                property: 'caption',
                type: 'string',
            ),
            new Property(
                property: 'link',
                type: 'string',
            )
        ]
    )]
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
