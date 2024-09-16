<?php

namespace Tests\Feature\User\Statistic;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserListTest extends TestCase
{
    public function test_request()
    {
        $startTime = now();
        $response = $this->get(route('api.v2.user.statistic.users-list', ['user' => 1]));
        
        $response->assertStatus(200); 
        dump($response->json());
        dump(sprintf('Work time %s seconds', now()->diffInSeconds($startTime)));
    }
}
