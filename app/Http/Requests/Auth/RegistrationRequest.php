<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="RegistrationRequest",
 *     title="Registration Request",
 *     description="Payload for user registration",
 *     type="object",
 *     required={"login", "referral_invited"},
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
 *         property="password",
 *         type="string",
 *         format="password",
 *         example="password123",
 *         description="User's password. Required with 'email'."
 *     ),
 *     @OA\Property(
 *         property="password_confirmation",
 *         type="string",
 *         format="password",
 *         example="password123",
 *         description="Confirmation of the user's password. Required with 'email'."
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         example="john_doe",
 *         description="User's login.",
 *     ),
 *     @OA\Property(
 *         property="referral_invited",
 *         type="string",
 *         example="ABC123",
 *         description="Referral invitation code.",
 *     ),
 *     @OA\Property(
 *         property="lang",
 *         type="string",
 *         enum={"ru", "eu"},
 *         example="ru",
 *         description="Preferred language (optional)"
 *     ),
 * )
 */
class RegistrationRequest extends FormRequest
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
            'phone' => ['required_without:email', 'integer', 'unique:users', 'min:10000000000', 'max:99999999999999'],
            'email' => ['required_without:phone', 'email', 'unique:users'],
            'password' => ['required_with:email', 'string', 'confirmed', 'min:8', 'max:12'],
            'login' => ['required', 'string', 'unique:users'],
            'referal_invited' => ['required', 'string', 'exists:users,referal_invited'],
            'lang' => ['nullable', 'in:ru,eu']
        ];
    }
}
