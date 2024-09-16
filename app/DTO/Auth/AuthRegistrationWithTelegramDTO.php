<?php

namespace App\DTO\Auth;

use App\Http\Requests\Auth\AuthRegistrationRequest;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthRegistrationWithTelegramDTO
{

    public function __construct(
        public string $login,
        public string|null $phone,
        public string $referal_id,
        public string $password,
        public string $lang = 'ru',
    ) {
    }

    public static function makeFromRequest(AuthRegistrationRequest $request): self
    {
        $refUserId = User::where(
            'referal_invited',
            $request->input('referal_invited')
        )->value('id');

        if (is_null($refUserId)) {
            throw new UnprocessableEntityHttpException('Для регистрации необходимо приглашение');
        }

        return new self(
            $request->input('login'),
            $request->input('phone'),
            $refUserId,
            $request->input('password'),
            $request->input('lang'),
        );
    }
}