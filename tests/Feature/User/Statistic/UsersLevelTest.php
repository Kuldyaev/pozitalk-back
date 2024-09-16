<?php

namespace Tests\Feature\User\Statistic;

use Tests\TestCase;


class UsersLevelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_request()
    {
        $startTime = now();
        $response = $this->get(route('api.v2.user.statistic.users-first-level', ['user' => 1]));
        
        $response->assertStatus(200); 
        
        dump($response->json());
        dump(sprintf('Work time %s seconds', now()->diffInSeconds($startTime)));
    }
}
