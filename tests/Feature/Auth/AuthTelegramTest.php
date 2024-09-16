<?php

namespace Tests\Feature\Auth;


use App\Models\User;
use Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTelegramTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $telegramId = '5967344403';

        $user = User::firstWhere("telegram_id", $telegramId);

        $this->assertNotNull($user);

        $token = config("telegram.bot.token");
        $request = Http::post("https://api.telegram.org/bot$token/getUserProfilePhotos", [
            'user_id' => $telegramId,
        ]);

        $photos = $request->json("result.photos", null);

        $photoData = Arr::last($photos);

        $this->assertNotEmpty($photoData);

        $request = Http::post("https://api.telegram.org/bot$token/getFile", [
            'file_id' => $fileId = Arr::last($photoData)["file_id"]
        ]);

        $filePath = $request->json("result.file_path", null);

        $resultPath =
            sprintf(
                "%s/%s.jpg",
                config("filesystems.dirs.user.avatar", "error"),
                $user->id,
            );
        
        copy(
            "https://api.telegram.org/file/bot{$token}/$filePath",
            $resultPath,
        );

        dump($photoData, $resultPath);

    }
}
