<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AvatarsRandomCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avatar:set-random';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseAvatarDir = storage_path('app/public/user/avatar/base/');
        $avatarDir = storage_path('app/public/user/avatar/');

        \File::ensureDirectoryExists($avatarDir);

        $baseAvatars = \Arr::map(
            \File::files($baseAvatarDir),
            fn(string $file) => basename($file)
        );

        $users = User::select('id', 'avatar')
            ->whereNull('avatar')
            ->get();

        $users->each(function (User $user) use ($baseAvatars, $baseAvatarDir, $avatarDir) {
            $avatar = \Arr::random($baseAvatars);
            $fileName = sprintf("%s.%s", $user->id, \File::extension($avatar));

            if (
                \File::copy(
                    $baseAvatarDir . $avatar,
                    $avatarDir . $fileName
                )
            ) {
                $user->update(['avatar' => $fileName]);   
            }
        });

        return Command::SUCCESS;
    }
}
