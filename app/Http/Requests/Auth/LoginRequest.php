<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="LoginRequest",
 *     title="Login Request",
 *     description="Payload for user login",
 *     type="object",
 *     required={"code"},
 *     @OA\Property(
 *         property="phone",
 *         type="integer",
 *         format="int64",
 *         example=1234567890,
 *         description="User's phone number. Required if 'email' and 'code' are not present."
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         example="ABC123",
 *         description="Verification code. Required if 'phone' and 'email' are not present."
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         format="email",
 *         example="user@example.com",
 *         description="User's email address. Required if 'phone' and 'code' are not present."
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         format="password",
 *         example="password123",
 *         description="User's password. Required with 'email'."
 *     ),
 * )
 */
class LoginRequest extends FormRequest
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
            'phone' => ['required_without_all:email,password', 'integer', 'min:10000000000', 'max:99999999999999', 'exists:users,phone'],
            'code' => ['required_without_all:email,password', 'string', 'exists:users,code'],
            'email' => ['required_without_all:phone,code', 'email', 'exists:users,email'],
            'password' => ['required_without_all:phone,code', 'string'],
        ];
    }
}
