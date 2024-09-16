<?php

namespace Tests\Feature\Auth;

use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{

    public function testSuccessRegistration()
    {

    }

    public function testInvalidFields()
    {

    }

    public function dataInvalidFields(): Generator
    {

        yield [
            'name' => 'test',
            'email' => 'test',
            'password' => 'test',
            'password_confirmation' => 'test',
        ];
    }
}
