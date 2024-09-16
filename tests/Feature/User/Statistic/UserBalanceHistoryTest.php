<?php

declare(strict_types=1);

namespace Tests\Feature\User\Statistic;

use Tests\TestCase;

class UserLoadBalanceHistoryTest extends TestCase
{
    public function test_request()
    {
        $startTime = now();
        $response = $this->get(route('api.v2.user.statistic.balance-history', ['user' => 1]));

        $response->assertStatus(200);

        dump($response->json());
        dump(sprintf('Work time %s seconds', now()->diffInSeconds($startTime)));
    }
}
