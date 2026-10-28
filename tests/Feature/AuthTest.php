<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['user' => ['id', 'name', 'email', 'created_at'], 'access_token', 'token_type']])
            ->assertJsonPath('data.user.email', 'ana@example.com')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonMissingPath('data.user.password');

        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_register_requires_valid_data(): void
    {
        $this->postJson('/api/v1/auth/register', ['email' => 'not-an-email', 'password' => '123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_register_rejects_an_email_already_in_use(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Ana',
            'email' => 'ana@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email' => 'El correo electrónico ya está registrado.']);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create(['email' => 'ana@example.com', 'password' => 'secret123']);

        $this->postJson('/api/v1/auth/login', ['email' => 'ana@example.com', 'password' => 'secret123'])
            ->assertOk()
            ->assertJsonStructure(['data' => ['user', 'access_token', 'token_type']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'ana@example.com', 'password' => 'secret123']);

        $this->postJson('/api/v1/auth/login', ['email' => 'ana@example.com', 'password' => 'wrong-password'])
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Credenciales inválidas.']);
    }

    public function test_login_is_rate_limited_per_email(): void
    {
        $credentials = ['email' => 'ana@example.com', 'password' => 'wrong-password'];

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', $credentials)->assertUnauthorized();
        }

        $this->postJson('/api/v1/auth/login', $credentials)
            ->assertTooManyRequests()
            ->assertHeader('Retry-After');

        $this->postJson('/api/v1/auth/login', ['email' => 'other@example.com', 'password' => 'x'])
            ->assertUnauthorized();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'No autenticado.']);
    }
}
