<?php

namespace App\Http\Resources\User\Statistic;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 */
class BalanceHistoryResource extends JsonResource
{

    #[Schema(
        schema: 'BalanceHistoryResource',
        properties: [
            new Property(
                property: 'user_id',
                type: 'integer',
            ),
            new Property(
                property: 'login',
                type: 'string',
            ),
            new Property(
                property: 'line',
                type: 'integer',
            ),
            new Property(
                property: 'date',
                type: 'string',
            ),
            new Property(
                property: 'sum',
                type: 'integer',
            ),
            new Property(
                property: 'type',
                type: 'string',
            ),
            new Property(
                property: 'balance',
                type: 'integer',
            ),
        ]
    )]
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'user_login' => $this->user->login,
            'user_name' => $this->name ?? $this->login,
            'user_avatar' => $this->avatar_url,
            'user_line' => $this->user->line,
            'sum' => $this->sum,
            'date' => $this->created_at->timestamp,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
