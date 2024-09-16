<?php

declare(strict_types=1);

namespace App\Actions\Auth\AuthTelegram;

use App\Actions\Auth\AuthRegistrationAction;
use App\Models\Auth\AuthProvider;
use App\Models\Auth\AuthProviderEnum;
use App\Models\Auth\AuthProviderStatusEnum;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\File;

class AuthOAuthTelegramAction extends AuthRegistrationAction
{
    use AuthOAuthTelegramTrait;

    public function run(User $user, array $rawData, bool $isCastDomain = false): AuthProvider
    {
        $data = $this->decodeTG($rawData, $isCastDomain);

        $finalUser = User::where('telegram_id', $data['id'])->first();

        if ($finalUser) {
            /** @var AuthProvider $authProvider */
            $authProvider = $finalUser->authProviders()
                ->where('provider', AuthProviderEnum::TELEGRAM)
                ->first();
            $user->delete();

            $authProvider ??= new AuthProvider([
                'user_id' => $finalUser->id
            ]);
        } else {
            $finalUser = $user;

            $authProvider = $user->authProviders()
                ->where('provider', AuthProviderEnum::TELEGRAM)
                ->first();
        }

        $finalUser->telegram_id = $data['id'];
        $finalUser->login = $data['username'];
        $finalUser->name = $data['first_name'] ?? $data['username'];

        if (isset($data['photo_url'])) {
            $fileName = sprintf("%s.jpg", $user->id);
            File::ensureDirectoryExists(storage_path('app/public/user/avatar/'));
            if (copy($data['photo_url'], storage_path('app/public/user/avatar/') . $fileName)) {
                $finalUser->avatar = $fileName;
            }
        }

        $authProvider->fill([
            'provider' => AuthProviderEnum::TELEGRAM,
            'provider_id' => $data['id'],
            'status' => AuthProviderStatusEnum::REGISTERED,
            'data' => $data,
        ]);

        DB::beginTransaction();
        $finalUser->save();
        $authProvider->save();
        DB::commit();

        return $authProvider;
    }
}
