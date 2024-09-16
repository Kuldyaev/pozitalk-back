<?php

namespace Tests\Feature\Auth;

use App\Actions\Auth\AuthRegistrationAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthNewPasswordTest extends TestCase
{
    use WithFaker;

    public function testSuccessNewPassword(): void
    {
        $this->setUpFaker();

        $currentPassword = $this->faker->password();
        $newPassword = $this->faker->password();

        $user = app(AuthRegistrationAction::class)->newUser([
            'login' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'referal_invited' => $this->faker->uuid(),
        ]);
        $user->password = Hash::make($currentPassword);
        $user->save();

        Sanctum::actingAs($user);
        $response = $this->patchJson(route('api.v2.auth.new-password'), [
            'password' => $currentPassword,
            'password_new' => $newPassword,
            'password_new_confirmation' => $newPassword,
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check($newPassword, $user->getOriginal('password')));
    }
}
