<?php

namespace App\Http\Requests\User\Statistic;

use App\Http\Requests\FormRequest;

class UserStatisticUserListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'sort_by' => 'string|in:name,line,status,first_line_count,organization_count,organization_total_sail_sum,organization_total_sail_sum',
            'sort_reverse' => 'boolean'
        ];
    }
}
