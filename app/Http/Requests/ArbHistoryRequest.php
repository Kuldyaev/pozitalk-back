<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArbHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'field' => 'nullable|string|in:created_at,status,count_months,amount,percent',
            'order' => 'nullable|string|in:asc,desc'
        ];
    }
}
