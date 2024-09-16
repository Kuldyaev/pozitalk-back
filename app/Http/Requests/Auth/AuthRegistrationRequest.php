<?php

namespace App\Http\Requests\Auth;

use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @property-read string $provider
 */
class AuthRegistrationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    #[Schema(
        schema: 'AuthRegistrationEmailRequest',
        type: 'object',
        properties: [
            new Property(
                property: 'email',
                type: 'string',
                example: 'email@example.com',
            ),
            new Property(
                property: 'password',
                type: 'string',
                example: 'pwd12oe@rw34',
            ),
            new Property(
                property: 'referal_invited',
                type: 'string',
                example: 'ODkwOTI5MjkyOTI=',
            ),
            new Property(
                property: 'lang',
                type: 'string',
                example: 'ru',
                enum: ['ru', 'eu'],
            ),
        ]
    )]
    public function rules()
    {
        $rules = [
            'login' => [
                'nullable',
                'string',
                fn($attr, $value, $fail) => $this->validateUniqueRegistered($attr, $value, $fail)
            ],
            'referal_invited' => ['required', 'string', 'exists:users,referal_invited'],
            'lang' => ['nullable', 'in:ru,en']
        ];

        $rules += match ($this->route()->parameter('provider')) {
            'email' => [
                'email' => [
                    'required',
                    'email',
                    fn($attr, $value, $fail) => $this->validateUniqueRegistered($attr, $value, $fail),
                ],
                'password' => ['required', 'string', 'min:8', 'max:12'],
            ],
            'telegram' => [
                'phone' => ['required', 'number', 'unique:users'],
            ]
        };

        return $rules;
    }

    private function validateUniqueRegistered(
        string $attribute,
        mixed $value,
        Closure $fail
    ) {
        $userId = User::where($attribute, $value)->value('id');
        if (is_null($userId)) {
            return;
        }

        if (
            AuthProvider::where('user_id', $userId)
                ->where('status', AuthProviderStatusEnum::REGISTERED->value)
                ->exists()
        ) {
            $fail(__('validation.unique', ['attribute' => $attribute]));
        }
    }
}
