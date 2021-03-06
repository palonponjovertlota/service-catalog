<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_login()
    {
        $attributes = [
            'email' => 'john@example.com',
            'password' => 'secret'
        ];

        $this->post(route('api.auth.signin'), $attributes)
            ->assertStatus(200)
            ->assertJsonStructure([
                'auth_token', 'token_type', 'expires_in'
            ]);
    }

    /** @test */
    public function a_user_can_be_found()
    {
        $user = _test_user();

        $this->get(route('api.auth.user', [
            'auth_token' => $user->auth_token
        ]))
            ->assertStatus(200)
            ->assertJson($user->toArray());
    }

    /** @test */
    public function a_user_can_signout()
    {
        $user = _test_user();

        $this->post(route('api.auth.signout'), [
            'auth_token' => $user->auth_token
        ])
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
            'auth_token' => null
        ]);
    }
}
