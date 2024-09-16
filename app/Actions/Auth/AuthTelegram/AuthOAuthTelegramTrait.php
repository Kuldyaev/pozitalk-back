<?php

namespace App\Actions\Auth\AuthTelegram;
use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;

trait AuthOAuthTelegramTrait
{
    protected function createProvider(User $user, bool $isCastDomain): AuthProvider
    {
        if (env('FEATURE_AUTHBOT_CAST', false) && $isCastDomain) {
            $botId = (int) env('FEATURE_AUTHBOT_CAST_TELEGRAM_BOT_ID');
            $url = sprintf(
                'https://oauth.telegram.org/auth?bot_id=%d&origin=%s/auth/%s/auth-callback',
                env('FEATURE_AUTHBOT_CAST_TELEGRAM_BOT_ID'),
                env('FEATURE_AUTHBOT_CAST_DOMAIN'),
                AuthProviderEnum::TELEGRAM->value
            );
        } else {
            $botId = (int) config('telegram.bot.id');
            $url = sprintf(
                'https://oauth.telegram.org/auth?bot_id=%d&origin=%s/auth/%s/auth-callback',
                config('telegram.bot.id'),
                config('app.front_url'),
                AuthProviderEnum::TELEGRAM->value
            );
        }

        $user->save();
        return $user->authProviders()->create([
            'provider' => AuthProviderEnum::TELEGRAM,
            'status' => AuthProviderStatusEnum::WAIT_PROVIDER,
            'data' => [
                // TODO: Remove feature flag
                'botId' => $botId,
                'url' => $url,
            ]
        ]);
    }

    protected function decodeTG(array $data, bool $isCastDomain): array
    {
        $hash = $data['hash'];
        unset($data['hash']);

        $preparedData = array_map(
            fn($value, $key) => $key . '=' . (string) $value,
            $data,
            array_keys($data)
        );
        sort($preparedData);

        $preparedData = implode("\n", $preparedData);

        // TODO: Remove feature flag
        if (env('FEATURE_AUTHBOT_CAST', false) && $isCastDomain) {
            $secretKey = hash('sha256', env('FEATURE_AUTHBOT_CAST_TELEGRAM_BOT_TOKEN'), true);
        } else {
            $secretKey = hash('sha256', config('telegram.bot.token'), true);
        }
        $checkHash = hash_hmac('sha256', $preparedData, $secretKey);

        if (0 !== strcmp($hash, $checkHash)) {
            throw new UnprocessableEntityHttpException(__('auth.telegram.invalid_hash'));
        }

        return $data;
    }
}