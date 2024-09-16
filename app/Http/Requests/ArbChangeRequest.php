<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArbChangeRequest extends FormRequest
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
            'id' => [
                'required',
                'integer',
                'exists:arb_deposits,id,user_id,' . auth()->user()->id,
            ],
            'count_months' => 'required|integer|in:6,12,18'
        ];
    }
}
