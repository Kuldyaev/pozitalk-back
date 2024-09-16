<?php

namespace App\Http\Resources\User\Statistic;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @mixin \App\Models\User
 */
class UserStatisticUsersListResource extends JsonResource
{
    #[Schema(
        schema: 'UserStatisticUsersListResource',
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
                property: 'line',
                type: 'integer',
                description: 'Номер линии'
            ),
            new Property(
                property: 'first_line_count',
                type: 'integer',
                description: 'Размер первой линии'
            ),
            new Property(
                property: 'organization_count',
                type: 'integer',
                description: 'Размер организации'
            ),
            new Property(
                property: 'organization_total_sail_sum',
                type: 'integer',
                description: 'Товарооборот организации'
            )
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
            'line' => $this->line,
            'first_line_count' => $this->first_line_count,
            'organization_count' => $this->organization_count,
            'organization_total_sail_sum' => $this->organization_total_sail_sum,
        ];
    }
}
