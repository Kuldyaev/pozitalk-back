<?php

namespace App\Http\Requests;

use App\Models\ArbDeposit;
use Illuminate\Foundation\Http\FormRequest;

class ArbReopenRequest extends FormRequest
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
                function ($attribute, $value, $fail) {
                    $deposit = ArbDeposit::find($value);

                    if ($deposit->is_active === true) {
                        return $fail('Депозит активен, закрыть нельзя.');
                    }

                    if ($deposit->status != 3) {
                        return $fail('Депозит должен закончиться.');
                    }
                },
            ],
        ];
    }
}
