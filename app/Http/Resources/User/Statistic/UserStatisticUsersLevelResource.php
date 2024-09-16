<?php

namespace App\Http\Resources\User\Statistic;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @mixin \App\Models\User
 */
class UserStatisticUsersLevelResource extends JsonResource
{
    #[Schema(
        schema: 'UserStatisticUsersLevelResource',
        properties: [
            new Property(
                property: 'id',
                type: 'integer',
            ),
            new Property(
                property: 'login',
                type: 'string',
            ),
            new Property(
                property: 'status',
                type: 'string',
            ),
            new Property(
                property: 'firstLineCount',
                type: 'integer',
                description: 'Размер первой линии'
            ),
            new Property(
                property: 'organizationCount',
                type: 'integer',
                description: 'Размер организации'
            ),
        ]
    )]
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'name' => $this->name ?? $this->login,
            'avatar' => $this->avatar_url,
            'status' => $this->status,
            'firstLineCount' => $this->first_line_count,
            'organizationCount' => $this->organization_count,
        ];
    }
}
