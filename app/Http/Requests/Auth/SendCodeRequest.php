<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="SendCodeRequest",
 *     title="Send Code Request",
 *     description="Payload for sending verification code",
 *     type="object",
 *     required={"lang"},
 *     @OA\Property(
 *         property="phone",
 *         type="integer",
 *         format="int64",
 *         example=1234567890,
 *         description="User's phone number. Required if 'email' is not present."
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="user@example.com",
 *         description="User's email address. Required if 'phone' is not present."
 *     ),
 *     @OA\Property(
 *         property="lang",
 *         type="string",
 *         enum={"ru", "en"},
 *         example="ru",
 *         description="Preferred language (optional)"
 *     ),
 * )
 */
class SendCodeRequest extends FormRequest
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
            'phone' => ['required_without_all:email', 'integer', 'min:10000000000', 'max:99999999999999', 'exists:users,phone'],
            'email' => ['required_without_all:phone', 'email', 'exists:users,email'],
            'lang' => ['nullable', 'in:ru,en']
        ];
    }
}
