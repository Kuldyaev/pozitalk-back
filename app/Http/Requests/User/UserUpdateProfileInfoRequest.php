<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Models\User\UserTelegramPolicyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Schema;


class UserUpdateProfileInfoRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string',
            'surname' => 'string',
            'avatar' => File::image()->between(50, 1024),
            'avatar_base' => 'string',
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'event_country' => 'string',
            'event_city' => 'string',
            'security_question' => 'string',
            'security_answer' => 'string',
            'telegram_policy' => ['nullable', 'string', Rule::in(UserTelegramPolicyEnum::slugs())],
        ];
    }
}
