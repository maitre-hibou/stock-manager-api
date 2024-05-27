<?php

declare(strict_types=1);

namespace Tests\Feature\API\Product;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_method_is_unavailable_to_unauthenticated_users(): void
    {
        $response = $this->post('/api/product');

        $response->assertStatus(401);
    }

    public function test_store_method_is_available_to_authenticated_users(): void
    {
        $response = $this->post('/api/product', [
            'title' => 'Test product',
            'price' => 123,
            'vat' => 20.0
        ], [
            'Authorization' => sprintf('Bearer %s', $this->getUserToken()),
        ]);

        $response->assertStatus(201);
    }

    public function test_store_method_returns_bad_request_with_invalid_data(): void
    {
        $token = $this->getUserToken();

        $response = $this->post('/api/product', [
            'price' => 123,
            'vat' => 20.0
        ], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(400);

        $response = $this->post('/api/product', [
            'title' => 'Test product',
            'description' => '',
            'price' => 123,
            'vat' => 20.0
        ], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(400);
    }

    protected function getUserToken(): string
    {
        $user = User::factory()->create();

        $tokenResponse = $this->post('/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        return json_decode($tokenResponse->getContent(), true)['access_token'];
    }
}
