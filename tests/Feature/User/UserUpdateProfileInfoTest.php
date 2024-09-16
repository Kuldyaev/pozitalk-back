<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\User\UserTelegramPolicyEnum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * @property \Faker\Generator $faker
 */
class UserUpdateProfileInfoTest extends TestCase
{
    use WithFaker;

    private function loginUser(): User
    {
        $user = User::find(1);
        Sanctum::actingAs($user, ['auth:login']);
        return $user;
    }

    public function testUpdateBaseInfo(): void
    {
        $this->setUpFaker();

        $securityQuestion = [
            'answer' => $this->faker->sentence(3),
            'question' => $this->faker->sentence(3),
        ];
        $telegramPolicySlug = $this->faker->randomElement(UserTelegramPolicyEnum::slugs());

        $newData = [
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'event_country' => $this->faker->country(),
            'event_city' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['male', 'female']),
        ];

        $this->loginUser();

        $response = $this->patchJson(
            route('api.v2.user.profile-info.update'),
            $newData + [
                'security_question' => $securityQuestion['question'],
                'security_answer' => $securityQuestion['answer'],
                'telegram_policy' => $telegramPolicySlug,
            ]
        );
        $response->assertStatus(200);

        $response->assertJsonIsObject('data');
        $this->assertDatabaseHas('users', $newData + [
            // 'security_question' => json_encode($securityQuestion),
            'telegram_policy' => UserTelegramPolicyEnum::fromSlug($telegramPolicySlug)->value,
        ]);
        $response->assertJson(['data' => $newData], false);

    }

    public function testUpdateAvatarFromBase(): void
    {
        $baseAvatarDir = storage_path('app/public/user/avatar/base/');
        $avatarDir = storage_path('app/public/user/avatar/');
        $user = $this->loginUser();

        $avatarPath = \Arr::random(\File::files($baseAvatarDir));
        $avatarFile = basename($avatarPath);

        $response = $this->patchJson(route('api.v2.user.profile-info.update'), [
            'avatar_base' => $avatarFile,
        ]);

        $user->refresh();
        $response->assertStatus(200);
        $this->assertFileEquals(
            $baseAvatarDir . $avatarFile,
            $avatarDir . basename($user->getOriginal('avatar'))
        );
    }

    public function testUpdateProfileAvatar(): void
    {
        $avatarDir = storage_path('app/public/user/avatar/');
        $avatarFile = UploadedFile::fake()->image('avatar.jpg')->size(501);
        $user = $this->loginUser();

        $response = $this->patchJson(route('api.v2.user.profile-info.update'), [
            'avatar' => $avatarFile,
        ]);

        $user->refresh();
        $response->assertStatus(200);
        $this->assertFileEquals(
            $avatarFile->path(),
            $avatarDir . basename($user->getOriginal('avatar'))
        );
    }

}
