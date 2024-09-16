<?php

namespace Tests;

use App\Actions\Auth\AuthRegistrationAction;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createAdminUser(): User
    {
        $user = app(AuthRegistrationAction::class)->newUser([
            'referal_invited' => Str::uuid(),
            'login' => Str::random(10),
            'role_id' => 2,
        ]);

        $user->save();
        Auth::login($user);
        $user->adminToken = Auth::refresh();
        return $user;
    }

    protected function createAndLoginUser(): User
    {
        $user = app(AuthRegistrationAction::class)->newUser([
            'referal_invited' => Str::uuid(),
            'login' => Str::random(10),
            'role_id' => 1,
        ]);
        $user->save();
        Sanctum::actingAs($user, ['auth:login']);
        return $user;
    }
}
