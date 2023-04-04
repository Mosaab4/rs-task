<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_success()
    {
        $request = $this->postJson(route('login'), [
            'email'    => $this->user->email,
            'password' => 'password',
        ]);

        $request->assertOk();

        $request->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'token',
            ],
        ]);
    }

    public function test_login_failed()
    {
        $request = $this->postJson(route('login'), [
            'email'    => $this->user->email,
            'password' => '1232123',
        ]);

        $request->assertUnauthorized();
    }
}
