<?php

declare(strict_types=1);

namespace App\Http\Controllers\User\Profile;

use Vi\Controllers\User\UserUpdateProfileInfoController as ViUserUpdateProfileInfoController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserUpdateProfileInfoRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Patch;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

class UserUpdateProfileInfoController extends Controller
{
    #[Patch(
        tags: ['User.Profile'],
        operationId: 'UserProfileInfoUpdate',
        path: '/user/profile-info',
        summary: 'Update user profile information',
        security: [['bearerAuth' => []]],
        requestBody: new RequestBody(
            content: new JsonContent(
                properties: [
                    new Property(
                        property: 'name',
                        type: 'string',
                        description: 'Имя'
                    ),
                    new Property(
                        property: 'surname',
                        type: 'string',
                        description: 'Фамилия'
                    ),
                    new Property(
                        property: 'event_country',
                        type: 'string',
                        description: 'Страна проведения мероприятия'
                    ),
                    new Property(
                        property: 'event_city',
                        type: 'string',
                        description: 'Город проведения мероприятия'
                    ),
                    new Property(
                        property: 'gender',
                        type: 'string',
                        description: 'Пол',
                        enum: ['male', 'female', 'other']
                    ),
                    new Property(
                        property: 'avatar',
                        type: 'file',
                        description: 'Аватар',
                        format: 'binary'
                    ),
                    new Property(
                        property: 'telegram_policy',
                        type: 'string',
                        description: 'Согласие на использование Telegram',
                        enum: ['for-referral', 'hidden', 'public'],
                    ),
                    new Property(
                        property: 'security_question',
                        type: 'string',
                        description: 'Заданный вопрос безопасности'
                    ),
                    new Property(
                        property: 'security_answer',
                        type: 'string',
                        description: 'Ответ на заданный вопрос безопасности'
                    ),
                ]
            )
        ),
        responses: [
            new Response(
                response: 200,
                description: 'OK',
                content: new JsonContent(properties: [
                    new Property(
                        property: 'data',
                        type: 'object',
                        ref: '#/components/schemas/User'
                    )
                ])
            ),
        ]
    )]
    public function __invoke(UserUpdateProfileInfoRequest $request): JsonResource
    {
        $controller = new ViUserUpdateProfileInfoController();
        return $controller($request);
    }
}
