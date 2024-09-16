<?php

namespace App\DTO\Auth;

use App\Http\Requests\Auth\AuthRegistrationRequest;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthRegistrationWithEmailDTO
{

    public function __construct(
        public string|null $login,
        public string|null $email,
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
            $request->input('login', Str::random(10)),
            $request->input('email'),
            $refUserId,
            $request->input('password'),
            $request->input('lang'),
        );
    }
}