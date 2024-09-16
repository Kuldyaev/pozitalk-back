<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTO\Auth\AuthRegistrationWithEmailDTO;
use App\Mail\EmailConfirmation;
use App\Mail\EmailConfirmationEu;
use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AuthRegistrationWithEmailAction extends AuthRegistrationAction
{
    public function run(AuthRegistrationWithEmailDTO $dto): AuthProvider
    {
        $user = User::where('email', $dto->email)
            ->first()
            ?? $this->newUser();

        if (
            AuthProvider::where('user_id', $user->id)
                ->where('status', AuthProviderStatusEnum::REGISTERED)
                ->exists()
        ) {
            throw new UnprocessableEntityHttpException('User login and password has already been registered');
        }

        $provider = $this->newProviderWithCode(AuthProviderEnum::EMAIL);

        $user->login = $dto->login;
        $user->referal_id = $dto->referal_id;
        $user->email = $dto->email;
        $user->password = Hash::make($dto->password);
        $user->referal_invited = Str::random();

        $code = $provider->getAttribute('data')['code'];
        $mail = match ($dto->lang) {
            'en' => new EmailConfirmationEu($code),
            'ru' => new EmailConfirmation($code),
        };

        Mail::to($user->email)->send($mail);

        DB::transaction(function () use ($user, $provider) {
            $user->save();
            $provider->user()->associate($user);
            $provider->save();
        });

        return $provider;
    }
}