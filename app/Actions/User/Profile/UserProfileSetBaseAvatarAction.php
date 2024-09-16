<?php

declare(strict_types=1);

namespace App\Actions\User\Profile;

use App\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Psy\Readline\Hoa\FileDoesNotExistException;

class UserProfileSetBaseAvatarAction
{
    public function run(User $user, string $avatar): bool
    {
        $avatarDir = storage_path('app/public/user/avatar/');
        $baseAvatarDir = storage_path('app/public/user/avatar/base/');
        \File::ensureDirectoryExists($avatarDir);
        if (!\File::exists($baseAvatarDir . $avatar)) {
            throw new FileNotFoundException('File not found in base avatar directory. Please check the file name. File: ' . $avatar);
        }

        $fileName = sprintf("%s.%s", $user->id, \File::extension($avatar));

        if (
            \File::copy(
                $baseAvatarDir . $avatar,
                $avatarDir . $fileName
            )
        ) {
            return $user->update(['avatar' => $fileName]);
        }

        return false;
    }
}