<?php

namespace App\Http\Requests\Auth;

use App\Models\Auth\AuthProvider;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

class AuthCheckCodeRequest extends FormRequest
{

    public AuthProvider|null $authProvider = null;

    public function authorize(): bool
    {
        return $this->user()->tokenCan('auth:validate-code');
    }

    #[Schema(
        schema: 'AuthValidateCodeRequest',
        title: 'Validate code for authentication',
        description: 'Validate code for authentication',
        type: 'object',
        properties:[
            new Property(
                property: 'code',
                type: 'string',
                example: '123456',
                description: 'Code from message in provider',
            ),
        ]
    )]
    public function rules()
    {
        return [
            'code' => [
                'required',
                'integer',
                function (string $attr, mixed $value, \Closure $fail) {
                    $this->authProvider = $this->user()
                        ->authProviders()
                        ->where('provider', $this->route()->parameter('provider'))
                        ->where('data->code', $value)
                        ->first();

                    if (!$this->authProvider) {
                        $fail(__('validation.exists', ['attribute' => $attr]));
                    }
                }
            ],
        ];
    }
}
