<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Mail\EmailConfirmation;
use App\Mail\EmailConfirmationEu;
use App\Models\Auth\AuthProvider;
use DB;
use Illuminate\Support\Facades\Config;
use Mail;

class AuthResendCodeWithEmailAction extends AuthRegistrationAction
{

    public function run(AuthProvider $provider): AuthProvider
    {
        $code = $this->generateCode();
        $provider->data = [
            'code' => $code,
            'code_generated_at' => now(),
        ];

        $lang = 'ru';
        $mail = match ($lang) {
            'en' => new EmailConfirmationEu($code),
            'ru' => new EmailConfirmation($code),
        };

        Mail::to($provider->user->email)->send($mail);
        
        $provider->save();

        return $provider;
    }
}