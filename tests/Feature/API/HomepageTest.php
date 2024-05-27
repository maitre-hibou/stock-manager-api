<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    public function test_api_home_returns_a_successful_response(): void
    {
        $response = $this->get('/api');

        $response->assertStatus(200);

        $response->assertJson([
            'version' => '0.0.1',
        ]);
    }
}
