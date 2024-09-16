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

class AuthOAuthAddTelegramAction extends AuthRegistrationAction
{
    use AuthOAuthTelegramTrait;

    public function run(User $user, array $rawData, bool $isCastDomain = false): AuthProvider
    {
        $data = $this->decodeTG($rawData, $isCastDomain);

        $authProvider = $user->authProviders()
            ->where('provider', AuthProviderEnum::TELEGRAM)
            ->first();

        $user->telegram_id = $data['id'];
        $user->name ??= $data['first_name'] ?? $data['username'];

        if (is_null($user->avatar)) {
            $fileName = sprintf("%s.jpg", $user->id);
            File::ensureDirectoryExists(storage_path('app/public/user/avatar/'));
            if (copy($data['photo_url'], storage_path('app/public/user/avatar/') . $fileName)) {
                $user->avatar = $fileName;
            }
        }

        $authProvider->fill([
            'provider_id' => $data['id'],
            'status' => AuthProviderStatusEnum::REGISTERED,
            'data' => $data,
        ]);

        DB::beginTransaction();
        $user->save();
        $authProvider->save();
        DB::commit();

        return $authProvider;
    }

}
