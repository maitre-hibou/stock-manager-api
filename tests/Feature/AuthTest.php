<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    public function test_login_method_returns_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'expires']);
    }

    public function test_login_method_fails_on_invalid_request(): void
    {
        $response = $this->post('/auth/login', [
            'email' => '',
            'password' => 'passwordddd',
        ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_login_method_fails_on_bad_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'passwordddd',
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        $response->assertJsonPath('message', sprintf('Unable to authenticate user with email %s', $user->email));
    }

    public function test_me_method_fails_for_unauthenticated_user(): void
    {
        $response = $this->get('/api/me');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_me_method_successful_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $tokenResponse = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $token = json_decode($tokenResponse->getContent(), true)['access_token'];

        $response = $this->get('/api/me', [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('email', $user->email);
    }
}
