<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_user_can_login_and_receive_token(): void
    {

        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email'     => $user->email,
            'password'  => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'token', 'user'
        ]);
    }

    public function test_user_cannot_login_with_incorrect_credentials(): void
    {

        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/login', [
            'email'     => $user->email,
            'password'  => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_user_can_register_and_receive_token(): void
    {
        $payload = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'passwordnew',
            'password_confirmation' => 'passwordnew',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertCreated();
        $response->assertJsonStructure([
            'token', 'user'
        ]);
        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
        ]);
    }

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $payload = [
            'name' => '',
            'email' => 'newuserexampleemailwrong',
            'password' => 'notMin',
            'password_confirmation' => 'passwordnew',
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
