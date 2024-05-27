<?php

declare(strict_types=1);

namespace Tests\Feature\API\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ListTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_product_is_successful(): void
    {
        $this->seed();

        $response = $this->get('/api/product');

        $response->assertStatus(200);

        $response->assertJsonPath('meta.total', 30);
    }
}
